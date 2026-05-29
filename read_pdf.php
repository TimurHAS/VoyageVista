<?php
$raw = file_get_contents('C:/Users/thier/Downloads/Sujet 4 - VoyageVista.pdf');
preg_match_all('/\(([^\x00-\x08\x0E-\x1F]{4,})\)/', $raw, $m);
$lines = array_filter($m[1], fn($s) => strlen($s) > 5 && preg_match('/[a-zA-Z]{3}/', $s));
echo implode("\n", array_slice($lines, 0, 500));
