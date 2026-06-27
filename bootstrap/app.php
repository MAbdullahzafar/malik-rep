    ->withMiddleware(function (Middleware $middleware) {
        // Tell Laravel to trust Vercel serverless proxy headers
        $middleware->trustProxies(at: '*');
    })
