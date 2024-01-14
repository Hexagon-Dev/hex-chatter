<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Hex-Chatter</title>
  @vite('resources/js/app.js')
  @vite('resources/css/app.css')
</head>
<body>
<div id="app" class="dark:bg-surface-900 bg-surface-300 dark:text-surface-100 text-surface-900"></div>
</body>
</html>
