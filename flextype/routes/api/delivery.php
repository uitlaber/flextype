<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Validate delivery token
 */
function validate_delivery_token($request, $flextype) : bool
{
    return Filesystem::has(PATH['tokens'] . '/delivery/' . $request->getQueryParams()['delivery_token'] . '/token.yaml');
}

/**
 * Fetch entry(entries)
 *
 * endpoint: /api/entries
 */
$app->get('/api/entries', function (Request $request, Response $response) use ($flextype) {

    // Get Query Params
    $query = $request->getQueryParams();

    // Set variables
    $id   = $query['id'];
    $args = isset($query['args']) ? $query['args'] : null;

    // Validate delivery token
    if (validate_delivery_token($request, $flextype)) {

        $delivery_token_file_path = PATH['tokens'] . '/delivery/' . $request->getQueryParams()['delivery_token'] . '/token.yaml';

        // Set delivery token file
        if ($delivery_token_file_data = $flextype['parser']->decode(Filesystem::read($delivery_token_file_path), 'yaml')) {
            if ($delivery_token_file_data['state'] == 'disabled' ||
                ($delivery_token_file_data['limit_calls'] != 0 && $delivery_token_file_data['calls'] >= $delivery_token_file_data['limit_calls'])) {
                return $response->withJson(["detail" => "Incorrect authentication credentials."], 401);
            } else {
                // Fetch entry
                $data = $flextype['entries']->fetch($id, $args);

                // Set response code
                $response_code = (count($data) > 0) ? 200 : 404 ;

                // Update calls counter
                Filesystem::write($delivery_token_file_path, $flextype['parser']->encode(array_replace_recursive($delivery_token_file_data, ['calls' => $delivery_token_file_data['calls'] + 1]), 'yaml'));

                // Return response
                return $response->withJson($data, $response_code);
            }
        } else {
            return $response->withJson(["detail" => "Incorrect authentication credentials."], 401);
        }
    } else {
        return $response->withJson(["detail" => "Incorrect authentication credentials."], 401);
    }
});
