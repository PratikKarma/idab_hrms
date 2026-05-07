<?php

namespace App\Services;

use Throwable;

class AutoFriendlyMessageService
{
    public static function generate(Throwable $e): string
    {
        $original = $e->getMessage();
        $message = strtolower(strip_tags($original));

        $replacements = [
            'not found' => 'missing',
            'controller' => 'page handler',
            'route' => 'page',
            'middleware' => 'app logic',
            'facade' => 'app module',
            'service provider' => 'core system',
            'container' => 'internal system',
            'config' => 'settings',
            'env' => 'environment setup',
            'artisan' => 'internal tool',

            'type error' => 'invalid input',
            'value error' => 'incorrect value',

            'path' => 'location',
            'file' => 'file or document',
            'folder' => 'section',
            'directory' => 'location',
            'storage' => 'server',
            'permission' => 'access rule',
            'read-only' => 'locked',
            'writable' => 'editable',
            'symlink' => 'shortcut',
            'stream' => 'connection',

            'sql' => 'data',
            'database' => 'our system',
            'query' => 'request',
            'table' => 'data list',
            'column' => 'data field',
            'row' => 'entry',
            'record' => 'item',
            'index' => 'reference',
            'foreign key' => 'relation',

            'xml' => 'data format',
            'form' => 'input form',
            'form data' => 'input data',
            'query string' => 'search terms',
            'payload' => 'data package',
            'response body' => 'output content',
            'response header' => 'output setting',
            'log' => 'system log',

            'syntax' => 'format',
            'deprecated' => 'outdated',
            'upload' => 'file upload',

            'model' => 'data item',
            'migration' => 'system update',
            'seeder' => 'sample data',
            'broadcast' => 'notification system',
            'notification' => 'alert',
            'event' => 'app event',
            'listener' => 'background process',
            'job' => 'task',
            'queue' => 'task manager',
            'blade' => 'page template',
            'view' => 'page layout',
            'binding' => 'system link',
            'closure' => 'internal function',
            'helper' => 'support tool',

            'undefined' => 'missing information',
            'invalid' => 'incorrect format',
            'unexpected' => 'something unexpected happened',
            'failed' => 'didn’t work',
            'null' => 'empty value',
            'object' => 'item',
            'array' => 'list',
            'function' => 'action',
            'method' => 'action',
            'class' => 'component',
            'parameter' => 'input value',
            'argument' => 'input',
            'variable' => 'value',
            'property' => 'setting',
            'cast' => 'convert',
            'strict' => 'strict format',

            'resource' => 'item',
            'handler' => 'process tool',
            'temp' => 'temporary',
            'cache' => 'saved data',
            'disk' => 'storage',

            'token' => 'security code',
            'csrf' => 'security check',
            'auth' => 'login system',
            'authentication' => 'login',
            'authorization' => 'access check',
            'forbidden' => 'not allowed',
            'unauthorized' => 'not allowed',
            'session' => 'your session',
            'timeout' => 'took too long to respond',
            'expired' => 'ran out of time',
            'secure' => 'protected',
            'ssl' => 'secure connection',

            'constraint' => 'data rule',
            'driver' => 'data connector',
            'connection' => 'data link',
            'transaction' => 'data process',
            'rollback' => 'undo change',
            'commit' => 'save change',
            'insert' => 'add data',
            'update' => 'change data',
            'delete' => 'remove data',

            'api' => 'service',
            'endpoint' => 'service point',
            'http' => 'web connection',
            'https' => 'secure web connection',
            'rest' => 'data service',
            'soap' => 'web data service',
            'graphql' => 'data request service',
            'websocket' => 'live connection',
            'webhook' => 'external callback',
            'request' => 'input',
            'response' => 'output',
            'header' => 'web setting',
            'cookie' => 'browser data',
            'status code' => 'response code',
            'status' => 'state',
            'rate limit' => 'too many requests',
            'throttle' => 'limited request',
            'cors' => 'access issue',
            'cors policy' => 'access rule',
            'json' => 'data format',

            'exception' => 'unexpected issue',
            'debug' => 'technical check',
            'warning' => 'system notice',
            'notice' => 'system message',
            'info' => 'system update',
            'critical' => 'important issue',
            'alert' => 'urgent message',
            'emergency' => 'critical issue',
            'pdo' => 'database connection',
            'stack trace' => 'error details',
            'trace' => 'technical details',
            'line' => 'line number',
            'file://' => 'file location',
            '.php' => 'code file',
            '.env' => 'system configuration',
            'vendor' => 'system package',
            'composer' => 'system setup',
            'call to' => 'triggered process',
            'instance of' => 'system type',
            'reflection' => 'system check',
            'error' => 'problem',
            'namespace' => 'system scope',
            'interface' => 'system definition',
            'trait' => 'shared function',
            'instance' => 'component',
            'static' => 'fixed value',
            'type' => 'format',
            'return' => 'result',
            'void' => 'no output',
            'parent' => 'base class',
            'public' => 'accessible',
            'protected' => 'restricted',
            'private' => 'confidential',
            'global' => 'shared',
            'self' => 'current class',
            'this' => 'current instance',
            'try' => 'attempt',
            'catch' => 'handle error',
            'throw' => 'raise issue',
            'class "' => 'component ',
            'function "' => 'process ',
            'method "' => 'action ',
        ];

        foreach ($replacements as $term => $friendly) {
            $message = str_replace($term, $friendly, $message);
        }

        if (
            strlen($message) < 8 ||
            preg_match('/(exception|sqlstate|stack trace|at |in |\.php|::|\(|\)|\[|\]|file:\/\/|\/var\/|c:\\\\|App\\\\|Illuminate\\\\|PDO)/i', $original)
        ) {
            return self::fallbackMessage();
        }

        $message = trim(ucfirst($message));
        if (!str_ends_with($message, '.')) {
            $message .= '.';
        }

        return $message;
    }

    protected static function fallbackMessage(): string
    {
        $friendlyOptions = [
            "Something didn’t work as expected. Please try again.",
            "Oops! We hit a small snag. Try again in a moment.",
            "We’re fixing something in the background. Please refresh the page.",
            "Something went wrong, but don’t worry — it’s not your fault.",
            "A temporary glitch occurred. It should be resolved soon.",
        ];

        return $friendlyOptions[array_rand($friendlyOptions)];
    }
}
