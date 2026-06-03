# medas-http-request-handler

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

The HTTP layer of the Medas framework. It handles the full lifecycle of an HTTP request: building the `Request` object from PHP superglobals, routing it to the correct `RequestHandler`, running authorization, dispatching the response through a prioritised chain of `ResponseHandler` implementations, and applying `ResponseModifier` plugins (e.g., CORS headers, cookies).

**Request lifecycle:**

1. `RequestFactory` reads `$_SERVER`, `$_FILES`, `$_COOKIE`, and `php://input`, parses the body (JSON / form-urlencoded / multipart), enforces the payload size limit, and caches the `Request` for the duration of the request.
2. `HttpRequestHandler::handle()` resolves the matching `RequestHandler` via `HttpRequestHandlerManager`, dispatches an `AuthorizationVote`, and calls `handle()` on the handler.
3. `ResponseDispatcher` runs all `ResponseModifier` implementations, then iterates `ResponseHandler` implementations in priority order until one returns `true`.
4. `OutputDataPrinter` sends the accumulated headers and output to the client.

**Response types:**

| Class / Interface | Handled by       | Priority |
|-------------------|------------------|----------|
| `JsonResponse`    | `JsonHandler`    | −10      |
| `HtmlResponse`    | `HtmlHandler`    | −20      |
| `FileResponse`    | `FileHandler`    | −30      |
| `JsonLdResponse`  | `JsonLdHandler`  | −40      |
| OPTIONS requests  | `OptionsHandler` | −50      |

**Parameter resolvers** — three `ParameterResolver` implementations inject request data into service constructors and handler parameters:

| Attribute                   | Resolver                    | Injects                                          |
|-----------------------------|-----------------------------|--------------------------------------------------|
| `#[BodyArgument('field')]`  | `BodyDataResolver`          | A dot-notation path from the parsed request body |
| `#[QueryArgument('param')]` | `QueryDataResolver`         | A named query string parameter                   |
| `#[RequestDataObject]`      | `RequestDataObjectResolver` | A typed DTO hydrated from body and/or query data |

**Authorization** — `AuthorizationVote` is dispatched as an event before the handler is called. Listeners set `$vote->allowedAccess` to `AllowedAccess::Allowed`, `Denied`, or `Abstain`. Two built-in vote handlers are provided: `AnyUserHandler` (grants access when `$request->authentication` is set) and `PublicResourceHandler` (always grants access when the handler is annotated with `#[PublicResource]`).

## Configuration options

| Option                                      | Default            | Description                              |
|---------------------------------------------|--------------------|------------------------------------------|
| `http-request-handler.cors-allowed-origins` | `[]`               | Allowed CORS origins; `['*']` for all    |
| `http-request-handler.cors-max-age`         | `86400`            | CORS preflight cache duration in seconds |
| `http-request-handler.max-payload-size`     | `10485760` (10 MB) | Maximum request body size in bytes       |

## Usage

### Package developer context

Register the package and wire the entry point:

```php
use Medas\HttpRequestHandler\HttpRequestHandlerPackage;

HttpRequestHandlerPackage::instance();
```

**Entry point** — call `HttpRequestHandler::handle()` from your `index.php`:

```php
service(\Medas\HttpRequestHandler\HttpRequestHandler::class)->handle();
```

**Defining a request handler:**

```php
use Medas\Core\Interfaces\HttpRequestHandler;
use Medas\Core\Attributes\Service;
use Medas\HttpRequestHandler\ResponseTypes\{JsonResponse, Response, SetsResponseCode};

#[Service]
readonly class GetInvoiceHandler implements HttpRequestHandler, JsonResponse, SetsResponseCode
{
    public function __construct(
        private InvoiceRepository $invoices,

        // Injected from the URI path segment by HttpRequestHandlerManager
        private int $id,
    ) {}

    public function handle(string $method, string $endpoint): Response
    {
        $invoice = $this->invoices->findById($this->id);

        return $this;
    }

    public function getJsonResponse(): mixed
    {
        return $this->invoices->findById($this->id)?->toArray();
    }

    public function responseCode(): int
    {
        return 200;
    }
}
```

**Injecting body and query data directly:**

```php
use Medas\HttpRequestHandler\Attributes\{BodyArgument, QueryArgument};
use Medas\Core\Attributes\Service;

#[Service]
readonly class CreateInvoiceHandler implements HttpRequestHandler
{
    public function __construct(
        #[BodyArgument('customer_id')]
        private int $customerId,

        #[BodyArgument('lines')]
        private array $lines,

        #[QueryArgument('draft')]
        private bool $draft = false,
    ) {}

    public function handle(string $method, string $endpoint): Response { /* ... */ }
}
```

Dot notation is supported for nested body fields: `#[BodyArgument('address.city')]`.

**Using a typed DTO:**

```php
use Medas\HttpRequestHandler\Attributes\RequestDataObject;
use Medas\Core\Attributes\Service;

class CreateInvoiceData
{
    public int $customerId;
    public array $lines = [];
    public bool $draft = false;
}

#[Service]
readonly class CreateInvoiceHandler implements HttpRequestHandler
{
    public function __construct(
        #[RequestDataObject(fromBody: true, fromQuery: true)]
        private CreateInvoiceData $data,
    ) {}

    public function handle(string $method, string $endpoint): Response { /* ... */ }
}
```

**Public resources** — mark a handler with `#[PublicResource]` to bypass authentication:

```php
use Medas\HttpRequestHandler\Authorization\PublicResource;
use Medas\Core\Interfaces\HttpRequestHandler;
use Medas\Core\Attributes\Service;

#[Service, PublicResource]
readonly class LoginHandler implements HttpRequestHandler { /* ... */ }
```

**Custom authorization** — listen to `AuthorizationVote` to implement access control:

```php
use Medas\HttpRequestHandler\Authorization\AuthorizationVote;
use Medas\Core\Attributes\{EventListener, Service};
use Medas\Core\Events\AllowedAccess;

#[Service]
readonly class RoleBasedAccessListener
{
    public function __construct(
        private CurrentUser $currentUser,
    ) {}

    #[EventListener]
    public function onAuthorizationVote(AuthorizationVote $vote): void
    {
        if ($vote->requestHandler instanceof AdminHandler
            && !$this->currentUser->hasRole('admin')) {
            $vote->allowedAccess = AllowedAccess::Denied;
        }
    }
}
```

**Custom response handler:**

```php
use Medas\HttpRequestHandler\ResponseHandlers\ResponseHandler;
use Medas\HttpRequestHandler\ResponseDispatcher\{ExceptionJob, Job};
use Medas\Core\Attributes\Service;

#[Service]
readonly class CsvHandler implements ResponseHandler
{
    public function priority(): int
    {
        return -15;
    }

    public function handleResponse(Job $job): bool
    {
        if (!$job->response instanceof CsvResponse) {
            return false;
        }

        $job->setHeader('Content-Type', 'text/csv');
        $job->setHeader('Content-Disposition', 'attachment; filename="export.csv"');
        $job->output = $job->response->toCsvString();

        return true;
    }

    public function handleException(ExceptionJob $job): bool
    {
        return false;
    }
}
```

Custom response handlers are discovered automatically via the service container.

**Returning a file:**

```php
use Medas\Core\File;
use Medas\HttpRequestHandler\ResponseTypes\FileResponse;

return new FileResponse(new File(
    content: file_get_contents('/path/to/invoice.pdf'),
    name: 'invoice-2026-05.pdf',
));
```

**Testing — injecting a mock request:**

```php
// Only works in CLI (PHP_SAPI === 'cli')
$requestFactory->set(new Request\Request(
    method: Request\Method::Post,
    uri: $uriManager->fromString('/invoices'),
    serverData: new Request\ServerData(['HTTP_ACCEPT' => 'application/json']),
    bodyData: new Request\BodyData(['customer_id' => 1, 'lines' => []]),
    fileData: new Request\FileData([]),
    cookieData: new Request\CookieData([]),
));
```

### Backend user context

**Configuring CORS:**

```yaml
http-request-handler:
  cors-allowed-origins:
    - https://app.example.com
    - https://admin.example.com
  cors-max-age: 3600
  max-payload-size: 5242880  # 5 MB
```

**Method override** — when a browser can only send `GET` or `POST`, include `::method` in the request parameters to override the HTTP method:

```html
<form method="POST">
  <input type="hidden" name="::method" value="DELETE" />
  …
</form>
```

**Exception handling** — uncaught exceptions propagate to `ExceptionDispatcher`, which iterates the same `ResponseHandler` chain calling `handleException()`. `JsonHandler` and `HtmlHandler` both implement exception rendering. Custom handlers can too by implementing `handleException(ExceptionJob $job): bool`. Exceptions implementing `DeclaresResponseCode` have their code used as the HTTP status code.
