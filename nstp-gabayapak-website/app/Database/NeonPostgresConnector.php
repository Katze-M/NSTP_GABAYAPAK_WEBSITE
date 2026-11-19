<?php

namespace App\Database;

use Illuminate\Database\Connectors\PostgresConnector;

/**
 * Neon routes connections by TLS SNI. A PHP build with an older bundled
 * libpq (common on Windows dev machines) won't send it, and the connection
 * fails with "Endpoint ID is not specified" (https://neon.com/sni). The
 * documented workaround is to pass the endpoint id via the `options`
 * parameter, which Laravel's stock PostgresConnector has no config slot for.
 */
class NeonPostgresConnector extends PostgresConnector
{
    protected function getDsn(array $config)
    {
        $dsn = parent::getDsn($config);

        if (isset($config['host']) && str_ends_with($config['host'], '.neon.tech')) {
            $endpointId = strtok($config['host'], '.');
            $dsn .= ";options='endpoint={$endpointId}'";
        }

        return $dsn;
    }
}
