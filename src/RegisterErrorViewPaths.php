<?php
namespace Diagro\Web;

use Illuminate\Support\Facades\View;

/**
 * Use: (new RegisterErrorViewPaths)();
 * In file/method: App\Exceptions\Handler#registerErrorViewPaths()
 *
 * @see https://laracasts.com/discuss/channels/laravel/custom-http-error-pages-from-package
 */
class RegisterErrorViewPaths
{

    /**
     * Register the error view paths.
     *
     * @return void
     */
    public function __invoke()
    {
        View::replaceNamespace('errors', collect(config('view.paths'))->map(function ($path) {
            return "{$path}/errors";
        })->push(__DIR__ . '/../resources/views/errors')->all());
    }

}