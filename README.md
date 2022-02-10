<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://diagro.be/assets/img/diagro-logo.svg" width="400"></a></p>

<p align="center">
<img src="https://img.shields.io/badge/project-lib_laravel_web-yellowgreen" alt="Diagro web library">
<img src="https://img.shields.io/badge/type-library-informational" alt="Diagro service">
<img src="https://img.shields.io/badge/php-8.0-blueviolet" alt="PHP">
<img src="https://img.shields.io/badge/laravel-8.67-red" alt="Laravel framework">
</p>

## Beschrijving

Deze bibliotheek wordt gebruikt als basis voor alle frontend webapplicaties in Laravel.

## Development

* Composer: `diagro/lib_laravel_web: "^1.0"`

## Production

* Composer: `diagro/lib_laravel_web: "^1.0"`

## Changelog

### V1.0

* **Feature**: login en logout functionaliteiten voor Diagro accounts (controllers, views en middlewares)
  * /login (name: login)
  * /logout (name: logout)
  * /company (name: company)
* **Feature**: middelware die automatisch de AAT token valideert.
* **Feature**: middelware voor routes die toegang hebben tot specifieke applicaties
* **Feature**: middelware voor routes die toegang hebben tot specifieke rollen
* **Feature**: blade helpers:
  * @can('read', $model)
  * @canRead($model)
  * @canCreate($model)
  * @canUpdate($model)
  * @canDelete($model)
  * @canPublish($model)
  * @canExport($model)
  * @hasApplication(string $application)
  * @hasRole(string $role)
* **Feature**: default API fail callback. Redirects to login or unauthorized page.