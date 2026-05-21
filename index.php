<?php

/**
 * Этот файл нужен только для OSPanel, где Document Root
 * указывает на корень проекта, а не на public/.
 *
 * Он просто перенаправляет все запросы в public/index.php.
 */

chdir(__DIR__);

$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/public/index.php';
$_SERVER['DOCUMENT_ROOT']   = __DIR__ . '/public';

require __DIR__ . '/public/index.php';