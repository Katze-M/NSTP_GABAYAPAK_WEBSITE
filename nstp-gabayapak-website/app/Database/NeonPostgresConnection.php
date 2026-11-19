<?php

namespace App\Database;

use Illuminate\Database\PostgresConnection;

/**
 * Pairs with NeonPostgresConnector: that connector forces emulated (client-side)
 * prepared statements, because Neon's pooled endpoint runs PgBouncer in
 * transaction mode, which doesn't support PDO's native prepared statements —
 * the second statement in any explicit transaction fails with "current
 * transaction is aborted" even though the first one appeared to succeed.
 *
 * Emulated prepares substitute bound values directly into the SQL text. The
 * base Connection casts PHP booleans to integers (1/0) before binding, which
 * is fine for native prepares (Postgres infers the parameter's real type from
 * context) but produces invalid SQL under emulation: `"approved" = 1` fails
 * with "operator does not exist: boolean = integer", since Postgres has no
 * implicit cast from a bare integer literal to boolean. A quoted string
 * literal ('1'/'0'), on the other hand, Postgres does implicitly coerce to
 * boolean — so bind it as a string instead.
 */
class NeonPostgresConnection extends PostgresConnection
{
    public function prepareBindings(array $bindings)
    {
        foreach ($bindings as $key => $value) {
            if (is_bool($value)) {
                $bindings[$key] = $value ? '1' : '0';
            }
        }

        return parent::prepareBindings($bindings);
    }
}
