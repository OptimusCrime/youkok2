<?php
namespace Youkok\Common\Middlewares;

use Illuminate\Database\Capsule\Manager as DB;
use Slim\Http\Request;
use Slim\Http\Response;

class DumpSqlLogMiddleware
{

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $response = $next($request, $response);

        if (getenv('DEV') !== '1') {
            return $response;
        }

        if (isset($_GET['DUMPSQL'])) {
            $queries = DB::connection()->getQueryLog();

            $output = [
                'num' => count($queries),
                'queries' => $queries,
            ];

            file_put_contents(getenv('CACHE_DIRECTORY') . 'db_dump_' . time() . '.json', json_encode($output));
        }

        return $response;
    }
}
