<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>{{$curso->titulo." ".$curso->headline}}</title>
  <link rel="icon" href="{{asset('/img/logo/logo-je-sm.png')}}" type="image/x-icon">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Meta description (descrição da página) -->
  <meta name="description" content="{{$curso->headline}}">
  <!-- Meta keywords (palavras-chave relacionadas ao conteúdo) -->
  <meta name="keywords" content="{{$curso->titulo}}, Curso, Certificado, Online, Profissionalização, Emprego, Educação, Carreira, Certificação, Portal Jovem Empreendedor, Programa Jovem Empreendedor, qualificação profissional, mercado de trabalho, capacitação profissional, curso com certificado, curso profissionalizante.">
  <!-- Meta author (autor do conteúdo) -->
  <meta name="author" content="Portal Jovem Empreendedor">
  <!-- Robots meta tag (controla indexação e rastreamento pelos bots de pesquisa) -->
  <meta name="robots" content="index, follow">

  <meta property="article:published_time" content="2024-10-24T08:00:00Z"> <!-- Data de publicação -->

<!-- Open Graph meta tags for Facebook and Instagram -->
<meta property="og:title" content="{{$curso->titulo." ".$curso->headline}}">
<meta property="og:description" content="{{$curso->headline}}">
<meta property="og:type" content="article"> <!-- Pode ser 'article', 'video', etc. -->
<meta property="og:url" content="https://jovemempreendedor.org/{{$curso->url}}"> <!-- URL canônica -->
<meta property="og:image" content="{{asset('/storage/'.$curso->capa_quadrada)}}"> <!-- URL da imagem de pré-visualização -->
<meta property="og:image:alt" content="{{$curso->titulo}}">
<meta property="og:site_name" content="Portal Jovem Empreendedor">
<meta property="og:locale" content="pt_BR">

<!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">-->

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    @charset "UTF-8";@font-face{font-display:block;font-family:bootstrap-icons;src:url("https://cdn.jsdelivr.net/npm/bootstrap-icons/font/fonts/bootstrap-icons.woff2?dd67030699838ea613ee6dbda90effa6") format("woff2"),url("https://cdn.jsdelivr.net/npm/bootstrap-icons/font/fonts/bootstrap-icons.woff?dd67030699838ea613ee6dbda90effa6") format("woff")}:root{--bs-blue:#0d6efd;--bs-indigo:#6610f2;--bs-purple:#6f42c1;--bs-pink:#d63384;--bs-red:#dc3545;--bs-orange:#fd7e14;--bs-yellow:#ffc107;--bs-green:#198754;--bs-teal:#20c997;--bs-cyan:#0dcaf0;--bs-black:#000;--bs-white:#fff;--bs-gray:#6c757d;--bs-gray-dark:#343a40;--bs-gray-100:#f8f9fa;--bs-gray-200:#e9ecef;--bs-gray-300:#dee2e6;--bs-gray-400:#ced4da;--bs-gray-500:#adb5bd;--bs-gray-600:#6c757d;--bs-gray-700:#495057;--bs-gray-800:#343a40;--bs-gray-900:#212529;--bs-primary:#0d6efd;--bs-secondary:#6c757d;--bs-success:#198754;--bs-info:#0dcaf0;--bs-warning:#ffc107;--bs-danger:#dc3545;--bs-light:#f8f9fa;--bs-dark:#212529;--bs-primary-rgb:13,110,253;--bs-secondary-rgb:108,117,125;--bs-success-rgb:25,135,84;--bs-info-rgb:13,202,240;--bs-warning-rgb:255,193,7;--bs-danger-rgb:220,53,69;--bs-light-rgb:248,249,250;--bs-dark-rgb:33,37,41;--bs-primary-text-emphasis:#052c65;--bs-secondary-text-emphasis:#2b2f32;--bs-success-text-emphasis:#0a3622;--bs-info-text-emphasis:#055160;--bs-warning-text-emphasis:#664d03;--bs-danger-text-emphasis:#58151c;--bs-light-text-emphasis:#495057;--bs-dark-text-emphasis:#495057;--bs-primary-bg-subtle:#cfe2ff;--bs-secondary-bg-subtle:#e2e3e5;--bs-success-bg-subtle:#d1e7dd;--bs-info-bg-subtle:#cff4fc;--bs-warning-bg-subtle:#fff3cd;--bs-danger-bg-subtle:#f8d7da;--bs-light-bg-subtle:#fcfcfd;--bs-dark-bg-subtle:#ced4da;--bs-primary-border-subtle:#9ec5fe;--bs-secondary-border-subtle:#c4c8cb;--bs-success-border-subtle:#a3cfbb;--bs-info-border-subtle:#9eeaf9;--bs-warning-border-subtle:#ffe69c;--bs-danger-border-subtle:#f1aeb5;--bs-light-border-subtle:#e9ecef;--bs-dark-border-subtle:#adb5bd;--bs-white-rgb:255,255,255;--bs-black-rgb:0,0,0;--bs-font-sans-serif:system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue","Noto Sans","Liberation Sans",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";--bs-font-monospace:SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;--bs-gradient:linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));--bs-body-font-family:var(--bs-font-sans-serif);--bs-body-font-size:1rem;--bs-body-font-weight:400;--bs-body-line-height:1.5;--bs-body-color:#212529;--bs-body-color-rgb:33,37,41;--bs-body-bg:#fff;--bs-body-bg-rgb:255,255,255;--bs-emphasis-color:#000;--bs-emphasis-color-rgb:0,0,0;--bs-secondary-color:rgba(33, 37, 41, 0.75);--bs-secondary-color-rgb:33,37,41;--bs-secondary-bg:#e9ecef;--bs-secondary-bg-rgb:233,236,239;--bs-tertiary-color:rgba(33, 37, 41, 0.5);--bs-tertiary-color-rgb:33,37,41;--bs-tertiary-bg:#f8f9fa;--bs-tertiary-bg-rgb:248,249,250;--bs-heading-color:inherit;--bs-link-color:#0d6efd;--bs-link-color-rgb:13,110,253;--bs-link-decoration:underline;--bs-link-hover-color:#0a58ca;--bs-link-hover-color-rgb:10,88,202;--bs-code-color:#d63384;--bs-highlight-color:#212529;--bs-highlight-bg:#fff3cd;--bs-border-width:1px;--bs-border-style:solid;--bs-border-color:#dee2e6;--bs-border-color-translucent:rgba(0, 0, 0, 0.175);--bs-border-radius:0.375rem;--bs-border-radius-sm:0.25rem;--bs-border-radius-lg:0.5rem;--bs-border-radius-xl:1rem;--bs-border-radius-xxl:2rem;--bs-border-radius-2xl:var(--bs-border-radius-xxl);--bs-border-radius-pill:50rem;--bs-box-shadow:0 0.5rem 1rem rgba(0, 0, 0, 0.15);--bs-box-shadow-sm:0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);--bs-box-shadow-lg:0 1rem 3rem rgba(0, 0, 0, 0.175);--bs-box-shadow-inset:inset 0 1px 2px rgba(0, 0, 0, 0.075);--bs-focus-ring-width:0.25rem;--bs-focus-ring-opacity:0.25;--bs-focus-ring-color:rgba(13, 110, 253, 0.25);--bs-form-valid-color:#198754;--bs-form-valid-border-color:#198754;--bs-form-invalid-color:#dc3545;--bs-form-invalid-border-color:#dc3545}*,::after,::before{box-sizing:border-box}@media (prefers-reduced-motion:no-preference){:root{scroll-behavior:smooth}}body{margin:0;font-family:var(--bs-body-font-family);font-size:var(--bs-body-font-size);font-weight:var(--bs-body-font-weight);line-height:var(--bs-body-line-height);color:var(--bs-body-color);text-align:var(--bs-body-text-align);background-color:var(--bs-body-bg);-webkit-text-size-adjust:100%}.h5,.h6,h2,h3,h5{margin-top:0;margin-bottom:.5rem;font-weight:500;line-height:1.2;color:var(--bs-heading-color)}h2{font-size:calc(1.325rem + .9vw)}@media (min-width:1200px){h2{font-size:2rem}}h3{font-size:calc(1.3rem + .6vw)}@media (min-width:1200px){h3{font-size:1.75rem}}.h5,h5{font-size:1.25rem}.h6{font-size:1rem}p{margin-top:0;margin-bottom:1rem}ul{padding-left:2rem}ul{margin-top:0;margin-bottom:1rem}strong{font-weight:bolder}small{font-size:.875em}a{color:rgba(var(--bs-link-color-rgb),var(--bs-link-opacity,1));text-decoration:underline}img{vertical-align:middle}button{border-radius:0}button,input{margin:0;font-family:inherit;font-size:inherit;line-height:inherit}button{text-transform:none}[type=button],[type=submit],button{-webkit-appearance:button}::-moz-focus-inner{padding:0;border-style:none}::-webkit-datetime-edit-day-field,::-webkit-datetime-edit-fields-wrapper,::-webkit-datetime-edit-hour-field,::-webkit-datetime-edit-minute,::-webkit-datetime-edit-month-field,::-webkit-datetime-edit-text,::-webkit-datetime-edit-year-field{padding:0}::-webkit-inner-spin-button{height:auto}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-color-swatch-wrapper{padding:0}::-webkit-file-upload-button{font:inherit;-webkit-appearance:button}::file-selector-button{font:inherit;-webkit-appearance:button}.lead{font-size:1.25rem;font-weight:300}.list-unstyled{padding-left:0;list-style:none}.img-fluid{max-width:100%;height:auto}.container,.container-fluid{--bs-gutter-x:1.5rem;--bs-gutter-y:0;width:100%;padding-right:calc(var(--bs-gutter-x) * .5);padding-left:calc(var(--bs-gutter-x) * .5);margin-right:auto;margin-left:auto}@media (min-width:576px){.container{max-width:540px}}@media (min-width:768px){.container{max-width:720px}}@media (min-width:992px){.container{max-width:960px}}@media (min-width:1200px){.container{max-width:1140px}}:root{--bs-breakpoint-xs:0;--bs-breakpoint-sm:576px;--bs-breakpoint-md:768px;--bs-breakpoint-lg:992px;--bs-breakpoint-xl:1200px;--bs-breakpoint-xxl:1400px}.row{--bs-gutter-x:1.5rem;--bs-gutter-y:0;display:flex;flex-wrap:wrap;margin-top:calc(-1 * var(--bs-gutter-y));margin-right:calc(-.5 * var(--bs-gutter-x));margin-left:calc(-.5 * var(--bs-gutter-x))}.row>*{flex-shrink:0;width:100%;max-width:100%;padding-right:calc(var(--bs-gutter-x) * .5);padding-left:calc(var(--bs-gutter-x) * .5);margin-top:var(--bs-gutter-y)}.col-3{flex:0 0 auto;width:25%}.col-8{flex:0 0 auto;width:66.66666667%}.col-9{flex:0 0 auto;width:75%}.col-12{flex:0 0 auto;width:100%}@media (min-width:576px){.col-sm-8{flex:0 0 auto;width:66.66666667%}}@media (min-width:768px){.col-md-7{flex:0 0 auto;width:58.33333333%}.col-md-12{flex:0 0 auto;width:100%}}@media (min-width:992px){.col-lg-6{flex:0 0 auto;width:50%}}.form-text{margin-top:.25rem;font-size:.875em;color:var(--bs-secondary-color)}.form-control{display:block;width:100%;padding:.375rem .75rem;font-size:1rem;font-weight:400;line-height:1.5;color:var(--bs-body-color);-webkit-appearance:none;-moz-appearance:none;appearance:none;background-color:var(--bs-body-bg);background-clip:padding-box;border:var(--bs-border-width) solid var(--bs-border-color);border-radius:var(--bs-border-radius)}.form-control::-webkit-date-and-time-value{min-width:85px;height:1.5em;margin:0}.form-control::-webkit-datetime-edit{display:block;padding:0}.form-control::-moz-placeholder{color:var(--bs-secondary-color);opacity:1}.form-control::-webkit-file-upload-button{padding:.375rem .75rem;margin:-.375rem -.75rem;-webkit-margin-end:.75rem;margin-inline-end:.75rem;color:var(--bs-body-color);background-color:var(--bs-tertiary-bg);border-color:inherit;border-style:solid;border-width:0;border-inline-end-width:var(--bs-border-width);border-radius:0}.btn{--bs-btn-padding-x:0.75rem;--bs-btn-padding-y:0.375rem;--bs-btn-font-size:1rem;--bs-btn-font-weight:400;--bs-btn-line-height:1.5;--bs-btn-color:var(--bs-body-color);--bs-btn-bg:transparent;--bs-btn-border-width:var(--bs-border-width);--bs-btn-border-color:transparent;--bs-btn-border-radius:var(--bs-border-radius);--bs-btn-hover-border-color:transparent;--bs-btn-box-shadow:inset 0 1px 0 rgba(255, 255, 255, 0.15),0 1px 1px rgba(0, 0, 0, 0.075);--bs-btn-disabled-opacity:0.65;--bs-btn-focus-box-shadow:0 0 0 0.25rem rgba(var(--bs-btn-focus-shadow-rgb), .5);display:inline-block;padding:var(--bs-btn-padding-y) var(--bs-btn-padding-x);font-family:var(--bs-btn-font-family);font-size:var(--bs-btn-font-size);font-weight:var(--bs-btn-font-weight);line-height:var(--bs-btn-line-height);color:var(--bs-btn-color);text-align:center;text-decoration:none;vertical-align:middle;border:var(--bs-btn-border-width) solid var(--bs-btn-border-color);border-radius:var(--bs-btn-border-radius);background-color:var(--bs-btn-bg)}.btn-light{--bs-btn-color:#000;--bs-btn-bg:#f8f9fa;--bs-btn-border-color:#f8f9fa;--bs-btn-hover-color:#000;--bs-btn-hover-bg:#d3d4d5;--bs-btn-hover-border-color:#c6c7c8;--bs-btn-focus-shadow-rgb:211,212,213;--bs-btn-active-color:#000;--bs-btn-active-bg:#c6c7c8;--bs-btn-active-border-color:#babbbc;--bs-btn-active-shadow:inset 0 3px 5px rgba(0, 0, 0, 0.125);--bs-btn-disabled-color:#000;--bs-btn-disabled-bg:#f8f9fa;--bs-btn-disabled-border-color:#f8f9fa}.fade:not(.show){opacity:0}.collapse:not(.show){display:none}.nav{--bs-nav-link-padding-x:1rem;--bs-nav-link-padding-y:0.5rem;--bs-nav-link-color:var(--bs-link-color);--bs-nav-link-hover-color:var(--bs-link-hover-color);--bs-nav-link-disabled-color:var(--bs-secondary-color);display:flex;flex-wrap:wrap;padding-left:0;margin-bottom:0;list-style:none}.nav-link{display:block;padding:var(--bs-nav-link-padding-y) var(--bs-nav-link-padding-x);font-size:var(--bs-nav-link-font-size);font-weight:var(--bs-nav-link-font-weight);color:var(--bs-nav-link-color);text-decoration:none;background:0 0;border:0}.nav-underline{--bs-nav-underline-gap:1rem;--bs-nav-underline-border-width:0.125rem;--bs-nav-underline-link-active-color:var(--bs-emphasis-color);gap:var(--bs-nav-underline-gap)}.nav-underline .nav-link{padding-right:0;padding-left:0;border-bottom:var(--bs-nav-underline-border-width) solid transparent}.nav-underline .nav-link.active{font-weight:700;color:var(--bs-nav-underline-link-active-color);border-bottom-color:currentcolor}.tab-content>.tab-pane{display:none}.tab-content>.active{display:block}.navbar{--bs-navbar-padding-x:0;--bs-navbar-padding-y:0.5rem;--bs-navbar-color:rgba(var(--bs-emphasis-color-rgb), 0.65);--bs-navbar-hover-color:rgba(var(--bs-emphasis-color-rgb), 0.8);--bs-navbar-disabled-color:rgba(var(--bs-emphasis-color-rgb), 0.3);--bs-navbar-active-color:rgba(var(--bs-emphasis-color-rgb), 1);--bs-navbar-brand-padding-y:0.3125rem;--bs-navbar-brand-margin-end:1rem;--bs-navbar-brand-font-size:1.25rem;--bs-navbar-brand-color:rgba(var(--bs-emphasis-color-rgb), 1);--bs-navbar-brand-hover-color:rgba(var(--bs-emphasis-color-rgb), 1);--bs-navbar-nav-link-padding-x:0.5rem;--bs-navbar-toggler-padding-y:0.25rem;--bs-navbar-toggler-padding-x:0.75rem;--bs-navbar-toggler-font-size:1.25rem;--bs-navbar-toggler-icon-bg:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2833, 37, 41, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");--bs-navbar-toggler-border-color:rgba(var(--bs-emphasis-color-rgb), 0.15);--bs-navbar-toggler-border-radius:var(--bs-border-radius);--bs-navbar-toggler-focus-width:0.25rem;position:relative;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;padding:var(--bs-navbar-padding-y) var(--bs-navbar-padding-x)}.navbar>.container-fluid{display:flex;flex-wrap:inherit;align-items:center;justify-content:space-between}.card{--bs-card-spacer-y:1rem;--bs-card-spacer-x:1rem;--bs-card-title-spacer-y:0.5rem;--bs-card-border-width:var(--bs-border-width);--bs-card-border-color:var(--bs-border-color-translucent);--bs-card-border-radius:var(--bs-border-radius);--bs-card-inner-border-radius:calc(var(--bs-border-radius) - (var(--bs-border-width)));--bs-card-cap-padding-y:0.5rem;--bs-card-cap-padding-x:1rem;--bs-card-cap-bg:rgba(var(--bs-body-color-rgb), 0.03);--bs-card-bg:var(--bs-body-bg);--bs-card-img-overlay-padding:1rem;--bs-card-group-margin:0.75rem;position:relative;display:flex;flex-direction:column;min-width:0;height:var(--bs-card-height);color:var(--bs-body-color);word-wrap:break-word;background-color:var(--bs-card-bg);background-clip:border-box;border:var(--bs-card-border-width) solid var(--bs-card-border-color);border-radius:var(--bs-card-border-radius)}.card-body{flex:1 1 auto;padding:var(--bs-card-spacer-y) var(--bs-card-spacer-x);color:var(--bs-card-color)}.accordion{--bs-accordion-color:var(--bs-body-color);--bs-accordion-bg:var(--bs-body-bg);--bs-accordion-border-color:var(--bs-border-color);--bs-accordion-border-width:var(--bs-border-width);--bs-accordion-border-radius:var(--bs-border-radius);--bs-accordion-inner-border-radius:calc(var(--bs-border-radius) - (var(--bs-border-width)));--bs-accordion-btn-padding-x:1.25rem;--bs-accordion-btn-padding-y:1rem;--bs-accordion-btn-color:var(--bs-body-color);--bs-accordion-btn-bg:var(--bs-accordion-bg);--bs-accordion-btn-icon:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none' stroke='%23212529' stroke-linecap='round' stroke-linejoin='round'%3e%3cpath d='M2 5L8 11L14 5'/%3e%3c/svg%3e");--bs-accordion-btn-icon-width:1.25rem;--bs-accordion-btn-icon-transform:rotate(-180deg);--bs-accordion-btn-active-icon:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none' stroke='%23052c65' stroke-linecap='round' stroke-linejoin='round'%3e%3cpath d='M2 5L8 11L14 5'/%3e%3c/svg%3e");--bs-accordion-btn-focus-box-shadow:0 0 0 0.25rem rgba(13, 110, 253, 0.25);--bs-accordion-body-padding-x:1.25rem;--bs-accordion-body-padding-y:1rem;--bs-accordion-active-color:var(--bs-primary-text-emphasis);--bs-accordion-active-bg:var(--bs-primary-bg-subtle)}.accordion-button{position:relative;display:flex;align-items:center;width:100%;padding:var(--bs-accordion-btn-padding-y) var(--bs-accordion-btn-padding-x);font-size:1rem;color:var(--bs-accordion-btn-color);text-align:left;background-color:var(--bs-accordion-btn-bg);border:0;border-radius:0;overflow-anchor:none}.accordion-button::after{flex-shrink:0;width:var(--bs-accordion-btn-icon-width);height:var(--bs-accordion-btn-icon-width);margin-left:auto;content:"";background-image:var(--bs-accordion-btn-icon);background-repeat:no-repeat;background-size:var(--bs-accordion-btn-icon-width)}.accordion-header{margin-bottom:0}.accordion-item{color:var(--bs-accordion-color);background-color:var(--bs-accordion-bg);border:var(--bs-accordion-border-width) solid var(--bs-accordion-border-color)}.accordion-item:first-of-type{border-top-left-radius:var(--bs-accordion-border-radius);border-top-right-radius:var(--bs-accordion-border-radius)}.accordion-item:first-of-type>.accordion-header .accordion-button{border-top-left-radius:var(--bs-accordion-inner-border-radius);border-top-right-radius:var(--bs-accordion-inner-border-radius)}.accordion-item:not(:first-of-type){border-top:0}.accordion-item:last-of-type{border-bottom-right-radius:var(--bs-accordion-border-radius);border-bottom-left-radius:var(--bs-accordion-border-radius)}.accordion-item:last-of-type>.accordion-header .accordion-button.collapsed{border-bottom-right-radius:var(--bs-accordion-inner-border-radius);border-bottom-left-radius:var(--bs-accordion-inner-border-radius)}.accordion-item:last-of-type>.accordion-collapse{border-bottom-right-radius:var(--bs-accordion-border-radius);border-bottom-left-radius:var(--bs-accordion-border-radius)}.accordion-body{padding:var(--bs-accordion-body-padding-y) var(--bs-accordion-body-padding-x)}.accordion-flush>.accordion-item{border-right:0;border-left:0;border-radius:0}.accordion-flush>.accordion-item:first-child{border-top:0}.accordion-flush>.accordion-item:last-child{border-bottom:0}.accordion-flush>.accordion-item>.accordion-header .accordion-button,.accordion-flush>.accordion-item>.accordion-header .accordion-button.collapsed{border-radius:0}.accordion-flush>.accordion-item>.accordion-collapse{border-radius:0}.list-group{--bs-list-group-color:var(--bs-body-color);--bs-list-group-bg:var(--bs-body-bg);--bs-list-group-border-color:var(--bs-border-color);--bs-list-group-border-width:var(--bs-border-width);--bs-list-group-border-radius:var(--bs-border-radius);--bs-list-group-item-padding-x:1rem;--bs-list-group-item-padding-y:0.5rem;--bs-list-group-action-color:var(--bs-secondary-color);--bs-list-group-action-hover-color:var(--bs-emphasis-color);--bs-list-group-action-hover-bg:var(--bs-tertiary-bg);--bs-list-group-action-active-color:var(--bs-body-color);--bs-list-group-action-active-bg:var(--bs-secondary-bg);--bs-list-group-disabled-color:var(--bs-secondary-color);--bs-list-group-disabled-bg:var(--bs-body-bg);--bs-list-group-active-color:#fff;--bs-list-group-active-bg:#0d6efd;--bs-list-group-active-border-color:#0d6efd;display:flex;flex-direction:column;padding-left:0;margin-bottom:0;border-radius:var(--bs-list-group-border-radius)}.list-group-item{position:relative;display:block;padding:var(--bs-list-group-item-padding-y) var(--bs-list-group-item-padding-x);color:var(--bs-list-group-color);text-decoration:none;background-color:var(--bs-list-group-bg);border:var(--bs-list-group-border-width) solid var(--bs-list-group-border-color)}.list-group-item:first-child{border-top-left-radius:inherit;border-top-right-radius:inherit}.list-group-item:last-child{border-bottom-right-radius:inherit;border-bottom-left-radius:inherit}.list-group-item+.list-group-item{border-top-width:0}.list-group-flush{border-radius:0}.list-group-flush>.list-group-item{border-width:0 0 var(--bs-list-group-border-width)}.list-group-flush>.list-group-item:last-child{border-bottom-width:0}.btn-close{--bs-btn-close-color:#000;--bs-btn-close-bg:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3c/svg%3e");--bs-btn-close-opacity:0.5;--bs-btn-close-hover-opacity:0.75;--bs-btn-close-focus-shadow:0 0 0 0.25rem rgba(13, 110, 253, 0.25);--bs-btn-close-focus-opacity:1;--bs-btn-close-disabled-opacity:0.25;--bs-btn-close-white-filter:invert(1) grayscale(100%) brightness(200%);box-sizing:content-box;width:1em;height:1em;padding:.25em;color:var(--bs-btn-close-color);background:transparent var(--bs-btn-close-bg) center/1em auto no-repeat;border:0;border-radius:.375rem;opacity:var(--bs-btn-close-opacity)}.modal{--bs-modal-zindex:1055;--bs-modal-width:500px;--bs-modal-padding:1rem;--bs-modal-margin:0.5rem;--bs-modal-bg:var(--bs-body-bg);--bs-modal-border-color:var(--bs-border-color-translucent);--bs-modal-border-width:var(--bs-border-width);--bs-modal-border-radius:var(--bs-border-radius-lg);--bs-modal-box-shadow:var(--bs-box-shadow-sm);--bs-modal-inner-border-radius:calc(var(--bs-border-radius-lg) - (var(--bs-border-width)));--bs-modal-header-padding-x:1rem;--bs-modal-header-padding-y:1rem;--bs-modal-header-padding:1rem 1rem;--bs-modal-header-border-color:var(--bs-border-color);--bs-modal-header-border-width:var(--bs-border-width);--bs-modal-title-line-height:1.5;--bs-modal-footer-gap:0.5rem;--bs-modal-footer-border-color:var(--bs-border-color);--bs-modal-footer-border-width:var(--bs-border-width);position:fixed;top:0;left:0;z-index:var(--bs-modal-zindex);display:none;width:100%;height:100%;overflow-x:hidden;overflow-y:auto;outline:0}.modal-dialog{position:relative;width:auto;margin:var(--bs-modal-margin)}.modal.fade .modal-dialog{transform:translate(0,-50px)}.modal-content{position:relative;display:flex;flex-direction:column;width:100%;color:var(--bs-modal-color);background-color:var(--bs-modal-bg);background-clip:padding-box;border:var(--bs-modal-border-width) solid var(--bs-modal-border-color);border-radius:var(--bs-modal-border-radius);outline:0}.modal-header{display:flex;flex-shrink:0;align-items:center;padding:var(--bs-modal-header-padding);border-bottom:var(--bs-modal-header-border-width) solid var(--bs-modal-header-border-color);border-top-left-radius:var(--bs-modal-inner-border-radius);border-top-right-radius:var(--bs-modal-inner-border-radius)}.modal-header .btn-close{padding:calc(var(--bs-modal-header-padding-y) * .5) calc(var(--bs-modal-header-padding-x) * .5);margin:calc(-.5 * var(--bs-modal-header-padding-y)) calc(-.5 * var(--bs-modal-header-padding-x)) calc(-.5 * var(--bs-modal-header-padding-y)) auto}.modal-title{margin-bottom:0;line-height:var(--bs-modal-title-line-height)}.modal-body{position:relative;flex:1 1 auto;padding:var(--bs-modal-padding)}@media (min-width:576px){.modal{--bs-modal-margin:1.75rem;--bs-modal-box-shadow:var(--bs-box-shadow)}.modal-dialog{max-width:var(--bs-modal-width);margin-right:auto;margin-left:auto}}.modal-fullscreen{width:100vw;max-width:none;height:100%;margin:0}.modal-fullscreen .modal-content{height:100%;border:0;border-radius:0}.modal-fullscreen .modal-header{border-radius:0}.modal-fullscreen .modal-body{overflow-y:auto}.offcanvas{--bs-offcanvas-zindex:1045;--bs-offcanvas-width:400px;--bs-offcanvas-height:30vh;--bs-offcanvas-padding-x:1rem;--bs-offcanvas-padding-y:1rem;--bs-offcanvas-color:var(--bs-body-color);--bs-offcanvas-bg:var(--bs-body-bg);--bs-offcanvas-border-width:var(--bs-border-width);--bs-offcanvas-border-color:var(--bs-border-color-translucent);--bs-offcanvas-box-shadow:var(--bs-box-shadow-sm);--bs-offcanvas-title-line-height:1.5}.offcanvas{position:fixed;bottom:0;z-index:var(--bs-offcanvas-zindex);display:flex;flex-direction:column;max-width:100%;color:var(--bs-offcanvas-color);visibility:hidden;background-color:var(--bs-offcanvas-bg);background-clip:padding-box;outline:0}.offcanvas.offcanvas-start{top:0;left:0;width:var(--bs-offcanvas-width);border-right:var(--bs-offcanvas-border-width) solid var(--bs-offcanvas-border-color);transform:translateX(-100%)}.offcanvas-header{display:flex;align-items:center;padding:var(--bs-offcanvas-padding-y) var(--bs-offcanvas-padding-x)}.offcanvas-header .btn-close{padding:calc(var(--bs-offcanvas-padding-y) * .5) calc(var(--bs-offcanvas-padding-x) * .5);margin:calc(-.5 * var(--bs-offcanvas-padding-y)) calc(-.5 * var(--bs-offcanvas-padding-x)) calc(-.5 * var(--bs-offcanvas-padding-y)) auto}.offcanvas-body{flex-grow:1;padding:var(--bs-offcanvas-padding-y) var(--bs-offcanvas-padding-x);overflow-y:auto}.fixed-top{position:fixed;top:0;right:0;left:0;z-index:1030}.align-text-top{vertical-align:text-top!important}.d-inline{display:inline!important}.d-inline-block{display:inline-block!important}.d-flex{display:flex!important}.shadow-sm{box-shadow:var(--bs-box-shadow-sm)!important}.border{border:var(--bs-border-width) var(--bs-border-style) var(--bs-border-color)!important}.w-100{width:100%!important}.flex-row{flex-direction:row!important}.justify-content-center{justify-content:center!important}.align-items-center{align-items:center!important}.my-0{margin-top:0!important;margin-bottom:0!important}.my-1{margin-top:.25rem!important;margin-bottom:.25rem!important}.mt-2{margin-top:.5rem!important}.mt-3{margin-top:1rem!important}.mt-4{margin-top:1.5rem!important}.mt-5{margin-top:3rem!important}.me-1{margin-right:.25rem!important}.me-2{margin-right:.5rem!important}.mb-0{margin-bottom:0!important}.mb-2{margin-bottom:.5rem!important}.mb-3{margin-bottom:1rem!important}.mb-4{margin-bottom:1.5rem!important}.mb-5{margin-bottom:3rem!important}.ms-1{margin-left:.25rem!important}.ms-2{margin-left:.5rem!important}.px-2{padding-right:.5rem!important;padding-left:.5rem!important}.py-0{padding-top:0!important;padding-bottom:0!important}.py-1{padding-top:.25rem!important;padding-bottom:.25rem!important}.py-2{padding-top:.5rem!important;padding-bottom:.5rem!important}.py-3{padding-top:1rem!important;padding-bottom:1rem!important}.py-5{padding-top:3rem!important;padding-bottom:3rem!important}.pt-5{padding-top:3rem!important}.pb-3{padding-bottom:1rem!important}.pb-5{padding-bottom:3rem!important}.fs-6{font-size:1rem!important}.fw-bold{font-weight:700!important}.fw-bolder{font-weight:bolder!important}.text-center{text-align:center!important}.text-decoration-none{text-decoration:none!important}.text-warning{--bs-text-opacity:1;color:rgba(var(--bs-warning-rgb),var(--bs-text-opacity))!important}.text-danger{--bs-text-opacity:1;color:rgba(var(--bs-danger-rgb),var(--bs-text-opacity))!important}.text-white{--bs-text-opacity:1;color:rgba(var(--bs-white-rgb),var(--bs-text-opacity))!important}.text-muted{--bs-text-opacity:1;color:var(--bs-secondary-color)!important}.bg-light{--bs-bg-opacity:1;background-color:rgba(var(--bs-light-rgb),var(--bs-bg-opacity))!important}.bg-white{--bs-bg-opacity:1;background-color:rgba(var(--bs-white-rgb),var(--bs-bg-opacity))!important}.bg-transparent{--bs-bg-opacity:1;background-color:transparent!important}.rounded{border-radius:var(--bs-border-radius)!important}.rounded-circle{border-radius:50%!important}@media (min-width:768px){.d-md-none{display:none!important}}.bi::before,[class*=" bi-"]::before{display:inline-block;font-family:bootstrap-icons!important;font-style:normal;font-weight:400!important;font-variant:normal;text-transform:none;line-height:1;vertical-align:-.125em;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.bi-award::before{content:"\f154"}.bi-briefcase::before{content:"\f1cc"}.bi-chat-left-quote::before{content:"\f250"}.bi-check-circle::before{content:"\f26b"}.bi-clipboard-check::before{content:"\f28b"}.bi-clock-fill::before{content:"\f291"}.bi-film::before{content:"\f3c3"}.bi-headset::before{content:"\f414"}.bi-list::before{content:"\f479"}.bi-person-check::before{content:"\f4d6"}.bi-play-circle::before{content:"\f4f3"}.bi-question-circle::before{content:"\f505"}.bi-share::before{content:"\f52e"}.bi-whatsapp::before{content:"\f618"}.bi-currency-dollar::before{content:"\f636"}#beneficios,#beneficios p,#bonus,#bonus li{color:#505a64}.bg-portal{background:linear-gradient(150deg,#0056b3,#181b1e)}.fw-bold{font-weight:700}.mb-1{margin-bottom:.25rem!important}.mt-0{margin-top:0!important}.btn-cta{display:inline-block;padding:15px 30px;font-size:.9rem;font-weight:700;color:#fff;text-decoration:none;background:linear-gradient(45deg,#007bff,#28a745);border:none;border-radius:5px;box-shadow:0 5px 15px rgba(0,0,0,.3);box-shadow:0 4px 15px rgba(0,0,0,.5)}.mx-auto{margin-right:auto!important;margin-left:auto!important}.navbar-brand{padding-top:var(--bs-navbar-brand-padding-y);padding-bottom:var(--bs-navbar-brand-padding-y);margin-right:var(--bs-navbar-brand-margin-end);font-size:var(--bs-navbar-brand-font-size);color:var(--bs-navbar-brand-color);text-decoration:none;white-space:nowrap}.text-decoration-none{color:#252525}.text-secondary{--bs-text-opacity:1;color:rgba(var(--bs-secondary-rgb),var(--bs-text-opacity))!important}.nav-link{color:#000}ul{list-style-type:none;padding:0;margin:0}ul li{padding:8px 0;border-bottom:1px solid #e0e0e0}ul li:last-child{border-bottom:none}.modal{z-index:99999}
        /*
        CORES DE FUNDO
        Exemplo: #007bff (Azul médio), #0056b3 (Azul escuro), #002147 (Azul escuro).
        Exemplo: #f8f9fa (Cinza muito claro), #e9ecef (Cinza claro).

        CORES DE FONTE
        Exemplo: #343a40 (Cinza escuro), #212529 (Preto suave).
        Exemplo: #ffffff (Branco).

        Cores de botões:
        Azul para botões de ação primária:Exemplo: #007bff (Azul primário), #0056b3 (Azul escuro para hover).
        Verde para ações seguras ou sucesso: Exemplo: #28a745 (Verde para sucesso), #218838 (Verde escuro para hover).
        Cores neutras (cinza) para botões secundários: Exemplo: #6c757d (Cinza médio).

        Exemplo de paleta:
        Fundo principal: #f8f9fa (Cinza muito claro)
        Fundo de seções destacadas: #007bff (Azul médio)
        Texto principal: #212529 (Preto suave)
        Botão primário: #007bff (Azul médio)
        Botão de sucesso: #28a745 (Verde sucesso)
        */

    #beneficios, #beneficios h5, #beneficios p, #bonus, #bonus li, #certificado{
        color: #505a64;
    }

     /**CORES PADRÃO DA PÁGINA**/
    /* #007bff #202020 #0056b3 background: linear-gradient(150deg, #0056b3, #181b1e);*/
    .bg-portal{background:  linear-gradient(150deg, #0056b3, #181b1e);}
    .bg-portal2{background: #002147;}
    .bg-portal3{background:  #f2f2f2;}
    .bg-portal4{background: linear-gradient(150deg, #28a745, #181b1e);}

    .color1{color: #505a64;}
    .color2{color: #007bff;}
    .color3{color: #28a745;}
    .color4{color: #fdfd88;}

    .fw-bold{
        font-weight: bold;
    }

    .mb-1 {
    margin-bottom: .25rem !important;
    }

    .mt-0 {
    margin-top: 0 !important;
    }

    .hero {
      background-image: url('{{asset('/storage/'.$curso->capa_vertical)}}');
      background-size: cover;
      background-position: center;
      position: relative;
      text-align: center;
      color: var(--accent-color);
      padding: 60px 20px;
    }

    .plyr__video-wrapper iframe{
        width: 600% !important; 
        margin-left: -250% !important;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7));
      z-index: 1;
    }

    .hero-content {
      position: relative;
      z-index: 2;
    }

    .hero h1 {
      font-size: 2rem;
      margin-bottom: 20px;
    }

    .hero h2 {
      font-size: 1.2rem;
      margin-bottom: 20px;
      color: var(--secondary-color);
    }

    .hero p {
      font-size: 1.1rem;
      margin-bottom: 20px;
    }

    /* Estilizando o botão CTA */
    .btn-cta {
      display: inline-block;
      padding: 15px 30px;
      font-size: 0.9rem;
      font-weight: bold;
      color: #fff;
      text-decoration: none;
      background: linear-gradient(45deg, #007bff, #28a745);
      border: none;
      border-radius: 5px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      cursor: pointer;
      box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.5);
    }

    .btn-cta:hover {
      background: linear-gradient(45deg, #28a745, #34d058);
    }

    /**PREÇO FIXO NO RODAPÉ**/
    .promo-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #003063e3;;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            z-index: 9999;
            opacity: 0; /* Começa invisível */
            visibility: hidden; /* Não ocupa espaço inicialmente */
            transition: opacity 0.5s ease-in-out, visibility 0.5s ease-in-out;
    }

    .top-banner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #ff6c00;
            color: white;
            text-align: center;
            padding: 10px 0;
            font-size: 1rem;
            font-weight: bold;
            z-index: 9999; /* Mantém o banner acima de outros elementos */
            /*white-space: nowrap; /* Garante que o texto fique em uma linha */
            overflow: hidden; /* Evita overflow caso o texto seja maior que o contêiner */
        }

    /**APENAS MOBILE**/
    @media (max-width: 768px) {

        /**CARROSSEL EMPRESAS**/
        .carousel-container-fluid {
            overflow: hidden; /* Esconde qualquer coisa fora da área de visualização */
            width: 100%; /* Ocupa 100% da largura do container-fluid */
        }

        .carousel-logos {
            display: flex;
            justify-content: space-around;
            align-items: center;
            width: calc(80px * 7); /* Ajuste a largura total para incluir o número de logos duplicadas */
            animation: scroll 20s linear infinite; /* Tempo da animação ajustado para a nova largura */
        }

        .carousel-logos img {
            max-width: 80px; /* Defina o tamanho máximo das logos */
            height: auto;
            margin: 0 15px;
            opacity: 0.8;
            transition: transform 0.2s ease, opacity 0.2s ease;
        }

        .carousel-logos img:hover {
            transform: scale(1.1); /* Aumenta um pouco ao passar o mouse */
            opacity: 1;
        }

        /* Animação de rolagem contínua */
        @keyframes scroll {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%); /* Mova metade da largura total */
            }
        }
    }

    /**APENAS DESKTOP**/
    @media (min-width: 769px) {
            .centralizar{justify-content: center !important;}
            #headline{
                max-width: 50%;
                margin-right: auto !important;
                margin-left: auto !important;}
            .hero {
                    background-image: url('{{asset('/storage/'.$curso->capa_horizontal)}}');
                    background-size: cover;
                    background-position: center;
                    position: relative;
                    text-align: center;
                    color: var(--accent-color);
                    padding: 60px 20px;
                }
            .hero::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(to bottom, rgba(0, 0, 0, 0.692), rgb(0, 0, 0));
                z-index: 1;
            }

            #rodape{
                padding-bottom: 50px;
            }

             /**CARROSSEL DESKTOP**/
            .carousel-logos img {
                    max-width: 80px; /* Defina o tamanho máximo das logos */
                    height: auto;
                    margin: 0 15px;
                    opacity: 0.8;
                    transition: transform 0.2s ease, opacity 0.2s ease;
            }
    }

    

  </style>
</head>
<body style="box-sizing: border-box;">
    <!--MENU-->
    <nav class="navbar fixed-top bg-portal">
        <div class="container-fluid d-flex align-items-center">
            <div class="row w-100">
                <div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-flex justify-content-between align-items-center">
                    <!-- Botão grande do menu -->
                    <button class="btn btn-light border" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu" aria-label="Nav Menu" style="padding: 5px 10px; font-size: 1rem;">
                        <i class="bi bi-list"></i>
                    </button>
                    <!-- Logo centralizada -->
                    <a href="#planos_pagamento" class="navbar-brand mx-auto text-center">
                        @if($curso->cidade) 
                            <p class="mt-0 fw-bold text-white" style="font-size: 1rem; margin-bottom: -7px;">Autorizado para <u class="text-uppercase">{{$curso->cidade}}.</u></p>
                            <p class="mt-1 mb-0 text-white" style="font-size: 0.8rem;">Inscrições terminam em <span class="countdown" style="font-size: 0.8rem;"></span></p>
                        @elseif($desconto_banner)
                            <p class="my-0 fw-bold text-uppercase text-white" style="font-size: 1rem;">Desconto especial de {{$desconto_banner}}%</p>
                            <p class="my-0 text-white" style="font-size: 0.8rem;">Termina em <span class="countdown"></span></p>
                        @else
                            <img src="{{asset('img/logo/logo-je-dark.png')}}" alt="Portal Jovem Empreendedor" width="140" height="31.5" class="d-inline-block align-text-top">    
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- MENU NAV -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel" >
        <div class="offcanvas-header shadow-sm" style="">
          <!--<h5 class="offcanvas-title" id="mobileMenuLabel">Menu</h5>-->
          <img src="{{asset('img/home_page/logojecolor.webp')}}" alt="Portal Jovem Empreendedor" width="200" height="52" class="d-inline-block align-text-top">
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="list-unstyled">
                <!-- Assistir aulas gratuitamente -->
                <li class="py-3">
                    <a href="#planos_pagamento" class="text-decoration-none " onclick="goToSection(event, 'planos_pagamento')">
                      <i class="bi bi-check-circle me-2"></i> Fazer minha inscrição
                    </a>
                </li>


                <li class="py-3">
                  <a data-bs-toggle="modal" data-bs-target="#aulas_demonstrativas" class="text-decoration-none " style="cursor: pointer">
                    <i class="bi bi-play-circle me-2"></i> Assistir aulas gratuitamente
                  </a>
                </li>
              
                <!-- Fale com um consultor pelo WhatsApp -->
                <li class="py-3">
                  <a target="_blank" href="#" href="https://wa.me/{{$curso->whatsapp_atendimento}}?text=Quero tirar minhas dúvidas sobre o curso {{$curso->titulo}}" class="text-decoration-none " >
                    <i class="bi bi-whatsapp me-2"></i> Fale com um consultor pelo WhatsApp
                  </a>
                </li>
                <!-- Certificado -->
                <li class="py-3">
                  <a href="#certificado" class="text-decoration-none " onclick="goToSection(event, 'certificado')">
                    <i class="bi bi-award me-2"></i> Certificado
                  </a>
                </li>
              
                <!-- Veja como é por dentro do curso -->
                <li class="py-3">
                  <a href="#video_dentro_curso" class="text-decoration-none " onclick="goToSection(event, 'video_dentro_curso')">
                    <i class="bi bi-film me-2"></i> Veja como é por dentro do curso
                  </a>
                </li>
              
                <!-- Depoimentos -->
                <li class="py-3">
                  <a href="#depoimentos" class="text-decoration-none " onclick="goToSection(event, 'depoimentos')">
                    <i class="bi bi-chat-left-quote me-2"></i> Depoimentos
                  </a>
                </li>
              
                <!-- Perguntas e Respostas -->
                <li class="py-3">
                  <a href="#perguntas_e_respotas" class="text-decoration-none " onclick="goToSection(event, 'perguntas_e_respotas')">
                    <i class="bi bi-question-circle me-2"></i> Perguntas e Respostas
                  </a>
                </li>
              </ul>
              <a class="mt-3 btn d-block btn-outline-dark" target="_blanck" href="{{$curso->link_area_membros}}"><i class="bi bi-box-arrow-in-right me-2"></i> Fazer Login e Entrar no Curso</a>
        </div>
    </div>  
    <section class="container-fluid pb-3 pt-5 mt-4">
        <div class="row">
            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">
                @if($curso->cidade OR $desconto_banner) 
                    <div class="text-center py-3">
                        <img src="{{asset('img/home_page/logojecolor.webp')}}" alt="Portal Jovem Empreendedor" width="140" height="36" class="d-inline-block align-text-top">  
                    </div>
                @endif
                <div id="div_aulas_gratuitas" class="video-facade" data-bs-toggle="modal" data-bs-target="#aulas_demonstrativas">
                    <img 
                        loading="lazy"  
                        src="{{asset('/storage/'.$curso->capa_horizontal)}}" 
                        alt="{{$curso->titulo}}" 
                        class="img-fluid img-lazy"
                    >
                    <!-- Degradê escuro -->
                    <div class="gradient-overlay"></div>
                    <!-- Botão de play -->
                    <div class="play-button">
                        <i class="bi bi-play-fill"></i>
                    </div>
                    <!-- Texto ajustado -->
                    <h4 class="fs-6 fw-bolder">Pré-visualizar este curso</h4>
                </div>


                <!-- MODAL AULAS DEMONSTRATIVAS -->
                <div class="modal fade" id="aulas_demonstrativas" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content bg-portal text-white">
                            <div class="modal-header">
                                <div class="modal-title" id="videoModalLabel">
                                    <p class="mb-0">Prévia do curso</p>
                                    <h5 class="fw-bolder">{{$curso->titulo}}</h5>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <!-- Área do vídeo -->
                                    @php
                                        $videotopo = $curso->video_apresentacao ?? $curso->video_dentro_do_curso;
                                    @endphp
                                    <div class="col-12 col-lg-8">
                                        <div id="modal_video_aula_demonstrativa" class="video-facade" data-video-id="{{$videotopo}}" onclick="loadVideo(this)">
                                            <img loading="lazy" src="https://img.youtube.com/vi/{{$videotopo}}/hqdefault.jpg" alt="{{$curso->titulo}}" class=" img-fluid img-lazy">
                                            <div class="play-button">
                                                <i class="bi bi-play-fill"></i> <!-- Ícone do Bootstrap -->
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Lista de aulas -->
                                    <div class="mt-3 col-12 col-lg-4">
                                        <h6 class="fw-bolder">Vídeos de amostra gratuitos:</h6>
                                        <ul class="list-group list-group-flush">
                                            @if(isset($curso->aulas_demonstrativas) AND !empty($curso->aulas_demonstrativas))
                                            @foreach($curso->aulas_demonstrativas as $aulas_demonstrativas)
                                            <li class="list-group-item bg-transparent d-flex align-items-center">
                                                <!-- Miniatura -->
                                                <img src="{{asset('/storage/'.$curso->capa_horizontal)}}" alt="Miniatura da Aula" class="rounded rounded-3 me-3" style="width: 40px; height: 40px;">
                                                
                                                <!-- Botão com ícone de play -->
                                                <button class="btn btn-link text-white p-0" style="text-align: left; text-decoration: none;" onclick="changeVideo('{{$aulas_demonstrativas['aula_id_youtube']}}')">
                                                    <!-- Ícone de play do Bootstrap 5 -->
                                                    <i class="bi bi-play-circle-fill me-2"></i>
                                                    {{$aulas_demonstrativas['aula_titulo']}}
                                                </button>
                                            </li>
                                            @endforeach
                                            @endif
                                            @if(!empty($curso->video_apresentacao))
                                            <li class="list-group-item bg-transparent d-flex align-items-center">
                                                <!-- Miniatura -->
                                                <img src="{{asset('/storage/'.$curso->capa_horizontal)}}" alt="Miniatura da Aula" class="rounded rounded-3 me-3" style="width: 40px; height: 40px;">
                                                
                                                <!-- Botão com ícone de play -->
                                                <button class="btn btn-link text-white p-0" style="text-align: left; text-decoration: none;" onclick="changeVideo('{{$curso->video_apresentacao}}')">
                                                    <!-- Ícone de play do Bootstrap 5 -->
                                                    <i class="bi bi-play-circle-fill me-2"></i>
                                                    Vídeo de apresentação
                                                </button>
                                            </li>
                                            @endif
                                            @if(!empty($curso->video_dentro_do_curso))
                                            <li class="list-group-item bg-transparent d-flex align-items-center">
                                                <!-- Miniatura -->
                                                <img src="{{asset('/storage/'.$curso->capa_horizontal)}}" alt="Miniatura da Aula" class="rounded rounded-3 me-3" style="width: 40px; height: 40px;">
                                                
                                                <!-- Botão com ícone de play -->
                                                <button class="btn btn-link text-white p-0" style="text-align: left; text-decoration: none;" onclick="changeVideo('{{$curso->video_dentro_do_curso}}')">
                                                    <!-- Ícone de play do Bootstrap 5 -->
                                                    <i class="bi bi-play-circle-fill me-2"></i>
                                                    Como é o curso por dentro
                                                </button>
                                            </li>
                                            @endif
                                        </ul>
                                        <div class="mt-3 text-center">
                                            <p class="fw-bold mb-2" style="line-height: 1.3">Inscreva-se agora mesmo <br>para liberar todas as aulas.</p>
                                            <a href="{{$curso->link_checkout_completo}}@if( $curso->origem != 'whatsapp')&sck=modal_aulas_demonstrativas @endif" class="btn-cta text-center text-uppercase btn-inscricao">Quero Me Inscrever Agora</a>
                                            <p class="mt-2 w-75 mx-auto fw-bold " style="font-size: x-small; color:#e9ecef;">Garanta seu acesso agora com 7 dias de garantia. Se não estiver satisfeito, devolvemos 100% do seu dinheiro!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
                <div class="card d-flex flex-row align-items-center py-3" style="max-width: 500px; border: none;">
                    <img alt="Analista de Qualidade" class="rounded" style="width: 70px; height: 70px; object-fit: cover;" src="{{asset('/storage/'.$curso->capa_vertical)}}" height="70px" width="70px">
                    <div class="ms-2">
                        <div class="d-flex align-items-center">
                        <span class="text-warning me-1">★</span>
                        <span class="fw-bold">{{ $curso->nota_avaliacao }}</span>
                        @php
                            if ($curso->numero_alunos<1000) {$curso->numero_alunos += 1058;}
                        @endphp
                        <span style="font-size: x-small;" class="text-muted ms-1">({{ $curso->numero_alunos }} alunos)</span>
                        <button style="font-size: xx-small;" class="share-button px-2 py-1 ms-2 btn btn-light border">
                        <i class="bi bi-share"></i> compartilhar
                        </button>
                        </div>
                        <h5 class="mt-0 fw-bolder mb-0">{{$curso->titulo}}</h5>
                        
                    </div>
                </div>
            </div>
        </div>
       
        <div class="row py-2">
            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">
                <div class="card py-0 mb-3">
                    <div class="card-body">
                      <p class="text-secondary mb-1 mt-0 " style="font-size: 0.8rem;">
                        <i class="bi bi-clock-fill"></i> 
                        Carga horária de {{ $curso->horas_completo }} horas
                      </p>
                      
                      <p class="text-secondary my-0 " style="font-size: 0.8rem;">
                        <i class="bi bi-currency-dollar"></i> 
                        Salário pode chegar a R${{ $curso->salario_maximo }}
                      </p>
                    </div>
                </div>
                <p>{{ $curso->headline }}</p>
                <p class="mb-0">Com o curso de <b>{{$curso->titulo}}</b>, você pode trabalhar em 
                    <i>@foreach($curso->areas_de_atuacao as $areas)
                      {{ $areas }},
                    @endforeach
                    </i>
                    e muito mais.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">
                <ul class="nav nav-underline mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active " id="conteudo-tab" data-bs-toggle="pill" data-bs-target="#conteudo" type="button" role="tab" aria-controls="conteudo" aria-selected="true">Conteúdo</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link " id="vantagens-tab" data-bs-toggle="pill" data-bs-target="#vantagens" type="button" role="tab" aria-controls="vantagens" aria-selected="false">Vantagens</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link " id="menu_bonus-tab" data-bs-toggle="pill" data-bs-target="#menu_bonus" type="button" role="tab" aria-controls="menu_bonus" aria-selected="false">Bônus</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link " id="menu_professor-tab" data-bs-toggle="pill" data-bs-target="#menu_professor" type="button" role="tab" aria-controls="menu_professor" aria-selected="false">Professor</button>
                      </li>
                  </ul>
            </div>
        </div>
    </section>
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="conteudo" role="tabpanel" aria-labelledby="conteudo-tab" tabindex="0">
            @if($curso->conteudo_principal_acordion)
            <section class="container-fluid pb-5">
                <div class="row">
                    <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">
                        <div class="accordion accordion-flush" id="accordionFlushExample">
                            @foreach($curso->conteudo_principal_acordion as $indice => $topico)
                            <div class="accordion-item">
                              <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse{{$indice}}" aria-expanded="false" aria-controls="flush-collapse{{$indice}}">
                                  {{$topico['title']}}
                                </button>
                              </h2>
                              <div id="flush-collapse{{$indice}}" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    @foreach($topico['topics'] as $conteudo)
                                        <p class="my-1">{{$conteudo}}</p>
                                    @endforeach
                                </div>
                              </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
            @endif
        </div>
        <div class="tab-pane fade" id="vantagens" role="tabpanel" aria-labelledby="vantagens-tab" tabindex="0">
            <!-- Por que este curso é para você -->
            <section id="beneficios" class="container-fluid pb-5">
                <div class="row">
                    <div class="col-sm-10 col-md-8 col-lg-6 mx-auto text-center">
                    <!-- Título da sessão -->
                        <h3 id="beneficios_h3" class="fw-bold">Por que este curso é para você?</h3>
                        <p class="lead text-center">Transforme seu futuro com uma profissão sólida e procurada no mercado!</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">
                    <!-- Benefícios do curso em forma de lista -->
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-transparent my-1">
                            <p class=" mb-0 fw-bold">
                                <i class="bi bi-briefcase"></i> Áreas de atuação
                            </p>
                            <p class="mb-0">Você pode trabalhar em 
                                @foreach($curso->areas_de_atuacao as $areas)
                                  {{ $areas }},
                                @endforeach
                                e muito mais.</p>
                        </li>
                        <li class="list-group-item bg-transparent my-1">
                        <p class=" mb-0 fw-bold">
                            <i class="bi bi-clipboard-check"></i> Habilidades mais buscadas
                        </p>
                        <p class="mb-0">Aprenda as competências que as empresas mais valorizam.</p>
                        </li>
                        <li class="list-group-item bg-transparent my-1">
                        <p class="mb-0 fw-bold">
                            <i class="bi bi-award "></i> Certificado reconhecido
                        </p>
                        <p class="mb-0">Com o nosso certificado, você estará pronto para conquistar oportunidades reais no mercado.</p>
                        </li>
                        <li class="list-group-item bg-transparent my-1">
                        <p class="mb-0 fw-bold">
                            <i class="bi bi-person-check "></i> Professores experientes
                        </p>
                        <p class="mb-0">Aprenda com quem entende! Professores qualificados ensinam de forma simples, mesmo para quem não tem experiência.</p>
                        </li>
                        <li class="list-group-item bg-transparent my-1">
                        <p class="mb-0 fw-bold">
                            <i class="bi bi-headset "></i> Suporte completo
                        </p>
                        <p class="mb-0">Acompanhe o curso com total apoio, incluindo dicas práticas para se destacar em entrevistas.</p>
                        </li>
                    </ul>
                    </div>
                </div>
            </section>
        </div>
        <div class="tab-pane fade" id="menu_bonus" role="tabpanel" aria-labelledby="menu_bonus-tab" tabindex="0">
            <!-- Seção BONUS -->
            <section id="bonus" class="container-fluid bg-white pb-5">
                <div class="row">
                    <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">
                        
                        <h3 class="fw-bold text-center">Bônus Exclusivos para você</h3>
                        <p class="lead text-center">Além de todo o conteúdo do curso, você ainda leva bônus incríveis que vão te preparar ainda mais para o mercado de trabalho. <strong>Sem pagar nada a mais.</strong></p>
                        @if(!$desconto_banner) 
                            <div class="text-center">
                                <p class="card px-2 py-1 d-inline text-danger text-center">Válido apenas para o plano completo.</p>
                            </div>
                        @endif

                        <div class="row mt-5 mb-2 d-flex align-items-center">
                            <div class="col-3">
                                <img loading="lazy"  alt='Portal Jovem Empreendedor'  class="  rounded-circle img-fluid img-lazy" src="{{asset('img/home_page/cartaestagio.webp')}}">
                            </div>
                            <div class="col-9" >
                                <div class="row d-flex align-items-center justify-content-center" style="min-height: 100px;">
                                    <p class="col-12 h6 feature-title"><strong>Carta de Estágio</strong></p>
                                    <p class="col-12 feature-description">Uma ferramenta poderosa para abrir portas no competitivo mercado de trabalho, oferecendo um grande diferencial em seu currículo.</p>
                                    <p class="col-12">DE <s>R$ 197,00</s> <span class="text-laranja">por R$ 0,00</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="row d-flex align-items-center ">
                            <div class="col-3">
                                <img loading="lazy"  alt='Portal Jovem Empreendedor'   class="  rounded-circle img-fluid img-lazy" src="{{asset('img/home_page/jovemaprendiz.webp')}}">
                            </div>
                            <div class="col-9">
                                <div class="row d-flex align-items-center justify-content-center" style="min-height: 100px;">
                                    <p class="col-12 h6 feature-title"><strong>Preparatório Jovem Aprendiz</strong></p>
                                    <p class="col-12 feature-description">Treinamento preparatório para você a ser entrar neste programa altamente competitivo.</p>
                                    <p class="col-12">DE <s>R$ 297,00</s> <span class="text-laranja">por R$ 0,00</span></p>
                                </div>
                            </div>
                        </div>
                        @if($curso->conteudo_bonus)
                        <div class="row">
                            <p class="h5 fs-6 fw-bold text-center mt-5">
                                Você também receberá de presente:
                            </p>
                            <ul class="list-group list-group-flush">
                                @forEach($curso->conteudo_bonus as $conteudo)
                                <li class="list-group-item"> {{$conteudo['title']}}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="row mt-4 text-center">
                            @if(!$desconto_banner) 
                                <p class="lead">
                                Esses bônus estão disponíveis apenas no plano completo!
                                </p>
                            @else
                                <p class="lead">
                                Esses bônus são oferecidos a todos os alunos que se inscrevem no curso!
                                </p>
                            @endif
                            
                            <p class=""> Não perca a chance de garantir uma formação completa e aumentar suas chances no mercado de trabalho.</p>
                        </div>
                </div>
            </section>
        </div>
        <div class="tab-pane fade" id="menu_professor" role="tabpanel" aria-labelledby="menu_professor-tab" tabindex="0">
            <section class="container">
                <div id="professor" class="row mb-5">
                    <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">
                        <div class="row mb-4 d-flex align-itens-center">
                            <div class="col-3 ">
                                <img loading="lazy"  alt='Portal Jovem Empreendedor' class='  img-fluid img-lazy rounded rounded-circle'  src='{{asset('/storage/'.$curso->professor_foto)}}'>
                            </div>
                            <div class="col-8">
                                <h5 class="fw-bold"><small>Conheça o(a) professor(a)</small> {{$curso->professor_nome}}</h5>
                            </div>
                        </div>
                        {!!$curso->professor_biografia!!}
                    </div>
                </div>
            </section>
        </div>
    </div>
    <section id="depoimentos" class="py-5 bg-portal text-white">
        <div class="container-fluid">
            <div class="row">
                <div class="text-center col-sm-10 col-md-8 col-lg-6 mx-auto">
                    <!-- Título da sessão -->
                    <h3 class="fw-bold ">O que nossos alunos têm a dizer?</h3>
                    <p class="lead">Nada melhor do que ouvir diretamente de quem já passou pela mesma jornada e conquistaram novas oportunidades.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">
                    @php
                        $depoimentos = [
                            'rejxwJ2lX-Q',
                            '1hekoAyPVRs',
                            'Mnn2yIAlhZk',
                            '9mmtunKAnMY',
                            'uQ5lB9r8ZlI',
                            'dMIxLKj35aU',
                            'gIV1MGief-0',
                            'X1IJZkVXgBw',
                            '1qWXa9F0qBw'
                        ];
                    @endphp
                    
                    <div id="video-depoimentos" class="mt-4 row flex-nowrap overflow-auto">
                        <!-- Vídeos de Depoimentos -->
                        @foreach($depoimentos as $depoimento)
                        <div class="col-sm-4 col-videos mb-4">
                            <div class="video-facade" data-video-id="{{ $depoimento }}" onclick="loadVideo(this)">
                                <img loading="lazy"  src="https://img.youtube.com/vi/{{ $depoimento }}/hqdefault.jpg" alt="Thumbnail {{ $depoimento }}" class=" img-fluid img-lazy">
                                <div class="play-button">
                                    <i class="bi bi-play-fill"></i> <!-- Ícone do Bootstrap -->
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="text-center col-sm-10 col-md-8 col-lg-6 mx-auto">
                    <!-- Título da sessão -->
                    <p>Agora que você viu como nossos alunos transformaram suas vidas, está na hora de <strong>você dar o próximo passo.</strong></p>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row py-5">
                <div class="text-center col-sm-10 col-md-8 col-lg-6 mx-auto">
                    <!-- Título da sessão -->
                    <h3 class="fw-bold ">Você não precisa esperar mais para começar a mudar de vida.</h3>
                    <p class="lead">Faça parte de um grupo de alunos que já estão conquistando suas vagas e crescendo na carreira.</p>
                </div>
            </div>
            <div class="row d-flex align-items-center">
                <div class="col-3 col-sm-1 offset-sm-4">
                    <img loading="lazy"  alt='Portal Jovem Empreendedor' class=" img-fluid img-lazy" src="{{asset('img/home_page/garantia-7-dias.png')}}" height="73px" width="73px">
                </div>
                <div class="col-9 col-sm-3">
                    <p>Tem dúvidas? Não se preocupe, o curso oferece uma <strong>garantia de 7 dias.</strong> Se não estiver satisfeito, devolvemos o seu dinheiro sem complicações.</p>
                </div>
                
            </div>
            <!--<div class="row mt-4">
                <div class="col-sm-10 col-md-8 col-lg-6 mx-auto text-center">
                    <p class="fw-bold mb-2" style="line-height: 1.3">Inscreva-se agora mesmo e comece agora sua jornada rumo ao sucesso!</p>
                    <a href="{{$curso->link_checkout_completo}}@if( $curso->origem != 'whatsapp')&sck=depoimentos @endif" class="btn-cta text-center text-uppercase btn-inscricao">Quero Me Inscrever Agora</a>
                    <p class="my-1 fw-bold color4" >Esta oferta acaba em <span class="countdown"></span></p>
                    <p class="mt-2 w-75 mx-auto fw-bold " style="font-size: x-small; color:#e9ecef;">Garanta seu acesso agora com 7 dias de garantia. Se não estiver satisfeito, devolvemos 100% do seu dinheiro!</p>
                </div>
            </div>-->
            
        </div>
    </section>
    <!-- Empresas LOGOS -->
    <section class="py-5 bg-white" style="text-align: center !important; background-color: white; padding-top: 3rem !important;padding-bottom: 3rem !important;">
        <div class="container-fluid color1">
        <!-- Ki - Introdução -->
        <h3 class="text-center fw-bold my-0" style="font-weight: 700 !important;box-sizing: border-box;font-size: calc(1.3rem + .6vw);">
            Mais de <strong class="color2">112 mil alunos</strong> em todo o Brasil e em +14 países.
        </h3>
        <p class="my-0 text-center fw-bold" style="font-size: small;">
            Nossos alunos já trabalham em empresas como:
        </p>
        
        <!-- Shō - Carrossel de Logotipos -->
        <div class="carousel-container-fluid mt-4">
            <div class="carousel-logos">
                <img loading="lazy"  src="{{asset('img/empresas/nestle.webp')}}" alt="Empresas onde nossos alunos trabalham" class="logos" width="80px" height="23px" >
                <img loading="lazy"  src="{{asset('img/empresas/coca.webp')}}" alt="Empresas onde nossos alunos trabalham" class="logos" width="80px" height="26px" >
                <img loading="lazy"  src="{{asset('img/empresas/ambev.webp')}}" alt="Empresas onde nossos alunos trabalham" class="logos" width="80px" height="45px" >
                <img loading="lazy"  src="{{asset('img/empresas/natura.webp')}}" alt="Empresas onde nossos alunos trabalham" class="logos" width="80px" height="61px" >
                <img loading="lazy"  src="{{asset('img/empresas/pao.webp')}}" alt="Empresas onde nossos alunos trabalham" class="logos" width="80px" height="64px" >
                <img loading="lazy"  src="{{asset('img/empresas/rener.webp')}}" alt="Empresas onde nossos alunos trabalham" class="logos" width="80px" height="25px" >
                <img loading="lazy"  src="{{asset('img/empresas/unilever.webp')}}" alt="Empresas onde nossos alunos trabalham" class="logos" width="80px" height="80px" >
            </div>
        </div>
        </div>
    </section>
    

    <!-- lista do que vai no curso -->
    <section id="planos_pagamento" class="py-5 px-2 text-white">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">
                    <div class="row flex-nowrap overflow-auto centralizar">
                        
                        @if(!$desconto_banner AND $curso->origem!='whatsapp')
                        <div class="col-11 col-sm-5 bg-portal4 p-3 border rounded rounded-3" style="margin-right: 2%;">
                            <h2 class="text-center" style="font-weight: bolder;color: #ffffba;font-size: 1.5rem;">PLANO BÁSICO</h2>
                            <div class="row">
                                <div class="text-center">
                                        <h3 class=" fw-bolder" style="font-size: 1rem;">O que você vai receber ao se inscrever?</h3>
                                        <p class="lead">Tudo que você precisa para conquistar seu emprego dos sonhos em um só curso!</p>
                                        <p>Ao se inscrever no curso de {{ ucfirst(strtolower($curso->titulo)) }}, você garante acesso a:</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <!-- Lista de Benefícios -->
                                    <ul class="list-unstyled">
                                    <li class="d-flex align-items-center my-1 px-3">
                                        <i class="bi bi-check-circle-fill text-white fs-4 me-3"></i>
                                        <span class="fw-normal text-white" style="font-size: small;">Acesso completo ao curso de {{ ucfirst(strtolower($curso->titulo)) }} com mais de {{$curso->horas_completo}} horas de conteúdo prático e fácil de seguir.</span>
                                    </li>
                                    <li class="d-flex align-items-center my-1 px-3">
                                        <i class="bi bi-check-circle-fill text-white fs-4 me-3"></i>
                                        <span class="fw-normal text-white" style="font-size: small;">Certificado de Conclusão reconhecido, pronto para turbinar o seu currículo.</span>
                                    </li>
                                    <li class="d-flex align-items-center my-1 px-3">
                                        <i class="bi bi-x-circle text-danger fs-4 me-3"></i>
                                        <span class="fw-normal text-white" style="font-size: small;"><del>Carta de Estágio para você buscar experiência prática e aumentar suas chances de contratação.</del></span>
                                    </li>
                                    <li class="d-flex align-items-center my-1 px-3">
                                        <i class="bi bi-x-circle text-danger fs-4 me-3"></i>
                                        <span class="fw-normal text-white" style="font-size: small;"><del>Curso Preparatório para Jovem Aprendiz em vídeo aulas.</del></span>
                                    </li>
                                    @if($curso->conteudo_bonus)  
                                    @forEach($curso->conteudo_bonus as $conteudo)
                                    <li class="d-flex align-items-center my-1 px-3">
                                        <i class="bi bi-x-circle text-danger fs-4 me-3"></i>
                                        <span class="fw-normal text-white" style="font-size: small;"><del>{{strtoupper($conteudo['title'])}}</del></span>
                                    </li>
                                    @endforeach
                                    @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="col-12 text-center text-white">
                                        
                                        <!--<p class="lead text-center">Além de tudo isso, você terá acesso aos materiais, podendo voltar sempre que precisar rever algum conteúdo ou atualizar suas habilidades!</p>-->
                                        <!--<p class="old-price my-0" style="font-size: small">de R${{$curso->preco_cheio}},00 por</p>-->
                                        
                                        @if($curso->origem!='whatsapp') <!--SÓ SE APLICA SE A PÁGINA NÃO FOR NO MÉTODO CARVALHO-->
                                        <p class="mt-3 fs-7 mb-0">Tudo isso de <span class="fs-6 fw-bold text-danger" style="text-decoration: line-through;">${{$curso->preco_cheio}},00</span> por apenas</p>
                                        <p class="mb-0 fw-bolder display-5 color4" style="margin-top: -10px">
                                            {{$curso->preco_parcelado_basico}}
                                        </p>
                                        <p class=" fs-6" style="margin-top: -10px">
                                            ou {{$curso->preco_cheio_basico}} à vista
                                        </p>
                                        @endif
                
                                        <div class="mt-4 text-center">
                                            <p class="fw-bold mb-2" style="line-height: 1.3">Inscreva-se agora mesmo e comece agora sua jornada rumo ao sucesso!</p><a href="{{$curso->link_checkout_basico}}@if( $curso->origem != 'whatsapp')&sck=plano_basico @endif" class="btn-cta text-center text-uppercase btn-inscricao">Quero Me Inscrever Agora</a>
                                            <p class="my-1 fw-bold color4" >Esta oferta acaba em <span class="countdown"></span></p>
                                            <p class="mt-2 w-75 mx-auto fw-bold " style="font-size: x-small; color:#e9ecef;">Garanta seu acesso agora com 7 dias de garantia. Se não estiver satisfeito, devolvemos 100% do seu dinheiro!</p>
                                        </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="@if(!$desconto_banner) col-sm-5 col-11 @else col-12 @endif p-3 bg-portal border rounded rounded-3 mx-auto">
                            <h2 class="text-center" style="font-weight: bolder;color: #ffffba;font-size: 1.5rem;">PLANO COMPLETO</h2>
                            <div class="row">
                                <div class="text-center">
                                        <h3 class=" fw-bolder" style="font-size: 1rem;">O que você vai receber ao se inscrever?</h3>
                                        <p class="lead">Tudo que você precisa para conquistar seu emprego dos sonhos em um só curso!</p>
                                        <p>Ao se inscrever no curso de {{ ucfirst(strtolower($curso->titulo)) }}, você garante acesso a:</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="">
                                    <!-- Lista de Benefícios -->
                                    <ul class="list-unstyled">
                                    <li class="d-flex align-items-center my-1 px-3">
                                        <i class="bi bi-check-circle-fill text-white fs-4 me-3"></i>
                                        <span class="fw-normal text-white" style="font-size: small;">Acesso completo ao curso de {{ ucfirst(strtolower($curso->titulo)) }} com mais de {{$curso->horas_completo}} horas de conteúdo prático e fácil de seguir.</span>
                                    </li>
                                    <li class="d-flex align-items-center my-1 px-3">
                                        <i class="bi bi-check-circle-fill text-white fs-4 me-3"></i>
                                        <span class="fw-normal text-white" style="font-size: small;">Certificado de Conclusão reconhecido, pronto para turbinar o seu currículo.</span>
                                    </li>
                                    <li class="d-flex align-items-center my-1 px-3">
                                        <i class="bi bi-check-circle-fill text-white fs-4 me-3"></i>
                                        <span class="fw-normal text-white" style="font-size: small;">Carta de Estágio para você buscar experiência prática e aumentar suas chances de contratação.</span>
                                    </li>
                                    <li class="d-flex align-items-center my-1 px-3">
                                        <i class="bi bi-check-circle-fill text-white fs-4 me-3"></i>
                                        <span class="fw-normal text-white" style="font-size: small;">Curso Preparatório para Jovem Aprendiz em vídeo aulas.</span>
                                    </li>
                                    @if($curso->conteudo_bonus)  
                                    @forEach($curso->conteudo_bonus as $conteudo)
                                    <li class="d-flex align-items-center my-1 px-3">
                                        <i class="bi bi-check-circle-fill text-white fs-4 me-3"></i>
                                        <span class="fw-normal text-white" style="font-size: small;">{{strtoupper($conteudo['title'])}}</span>
                                    </li>
                                    @endforeach
                                    @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="text-center text-white">
                                        
                                        <!--<p class="lead text-center">Além de tudo isso, você terá acesso ao curso e seus materiais, podendo voltar sempre que precisar rever algum conteúdo ou atualizar suas habilidades!</p>-->
                                        <!--<p class="old-price my-0" style="font-size: small">de R${{$curso->preco_cheio}},00 por</p>-->
                                        
                                        @if($curso->origem!='whatsapp') <!--SÓ SE APLICA SE A PÁGINA NÃO FOR NO MÉTODO CARVALHO-->
                                        <p class="mt-3 fs-7 mb-0">Tudo isso de <span class="fs-6 fw-bold text-danger" style="text-decoration: line-through;">${{$curso->preco_cheio}},00</span> por apenas</p>
                                        <p class="mb-0 fw-bolder display-5 color4" style="margin-top: -10px">
                                            {{$curso->preco_parcelado_completo}}
                                        </p>
                                        <p class=" fs-6" style="margin-top: -10px">
                                            ou {{$curso->preco_cheio_completo}} à vista
                                        </p>
                                        @endif
                
                                        <div class="mt-4 text-center">
                                            <p class="fw-bold mb-2" style="line-height: 1.3">Inscreva-se agora mesmo e comece agora sua jornada rumo ao sucesso!</p><a href="{{$curso->link_checkout_completo}}@if( $curso->origem != 'whatsapp')&sck=plano_completo @endif" class="btn-cta text-center text-uppercase btn-inscricao">Quero Me Inscrever Agora</a>
                                            <p class="my-1 fw-bold color4" >Esta oferta acaba em <span class="countdown"></span></p>
                                            <p class="mt-2 w-75 mx-auto fw-bold " style="font-size: x-small; color:#e9ecef;">Garanta seu acesso agora com 7 dias de garantia. Se não estiver satisfeito, devolvemos 100% do seu dinheiro!</p>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            
            
            
        </div>
    </section>

    <!-- Seção Certificado -->
    <section id="certificado" class="container-fluid bg-white py-5">
        <div class="row">
            <div class="text-center col-sm-10 col-md-8 col-lg-6 mx-auto">
                <h3 class="fw-bold ">Certificado <strong >reconhecido e registrado</strong></h3>
                <p class='lead'>Você receberá um <strong> certificado de conclusão com validade em todo o Brasil.</strong> </p>
                        <p>Nosso certificado oferece uma série de benefícios para garantir sua credibilidade no mercado de trabalho.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">
                <div class="card border-0 text-center">
                    <img loading="lazy"  class="rounded mx-auto img-fluid img-lazy" alt="Certificado" src="{{asset('img/home_page/certificadoNovo2.webp')}}" width="364px" height="257.35px">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="mt-4 col-sm-10 col-md-8 col-lg-6 mx-auto">
                
                <div class="my-4">
                    <p class="mb-0 d-flex align-items-center">
                        <i class="bi bi-patch-check-fill  me-2"></i>
                        <strong>Registro digital com QR Code:</strong>
                    </p>
                    <p class="mt-0" style="font-size: small">O certificado possui assinatura digital e um QR code exclusivo, o que garante sua autenticidade e facilita a verificação online por recrutadores e empresas.</p>
                </div>
                
                <div class="mb-4">
                    <p class="mb-0 d-flex align-items-center">
                        <i class="bi bi-bookmark-star-fill  me-2"></i>
                        <strong>Mesma validade de um curso presencial:</strong>
                    </p>
                    <p class="mt-0" style="font-size: small">O certificado tem a mesma validade legal de cursos presenciais, reconhecido por empresas e instituições de ensino.</p>
                </div>
                
                <div class="">
                    <p class=" mb-0 d-flex align-items-center">
                        <i class="bi bi-award-fill  me-2"></i>
                        <strong>Extensão universitária e concursos públicos:</strong>
                    </p>
                    <p class="mt-0" style="font-size: small">Nosso certificado pode ser utilizado como extensão universitária e é aceito em concursos públicos, desde que esteja em conformidade com os requisitos do edital.</p>
                </div>
                
            </div>
        </div>
        <div class="row mt-4">
            <div class="text-center col-sm-10 col-md-8 col-lg-6 mx-auto">
                <p class="lead ">Assim que você concluir o curso, terá acesso ao certificado 100% digital. Ele pode ser baixado e impresso ou anexado diretamente ao seu currículo e perfis profissionais.</p>
                <p><strong>Comprove sua qualificação e dê o próximo passo em sua carreira.</strong> 

                </p>
                <p>Garanta seu certificado ao final do curso e abra as portas para novas oportunidades.</p>
            </div>
        </div>
    </section>

    <!-- Seção Chame no WhatsApp -->
    <section class="py-5 text-center bg-portal3">
        <div class="container-fluid color1">

        <div class="row">
            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">
                    <!-- Título -->
                    <h3 class="fw-bold ">Tem dúvidas? Fale com a gente!</h2>
                        
                    <!-- Ki - Introdução -->
                    <p class="lead text-center">
                        Ainda com dúvidas sobre o curso? <br>
                        Nosso time está pronto para te ajudar! Tire todas as suas dúvidas agora mesmo, é só chamar no WhatsApp.
                    </p>
                    
                    <!-- Shō - Desenvolvimento -->
                    <p class="mb-4">
                        Estamos aqui para te oferecer todo o suporte que você precisa. Seja para saber mais sobre o curso, formas de pagamento ou qualquer outra questão, fale diretamente com a nossa equipe.
                    </p>
                    
                    <!-- Ten - Reviravolta -->
                    <p class="fw-bold mb-2">
                        Responda suas dúvidas na hora e tenha a certeza de que está fazendo a melhor escolha para sua carreira!
                    </p>
                    
                    <!-- Ketsu - Conclusão -->
                    <a href="https://wa.me/{{$curso->whatsapp_atendimento}}?text=Quero tirar minhas dúvidas sobre o curso {{$curso->titulo}}" target="_blank" class="btn-cta fw-bold btn-inscricao">
                        <i class="bi bi-whatsapp"></i> Fale com a gente no WhatsApp
                    </a>
                    <p class="mt-1 fw-bold" style="font-size: small">Clique no botão para conversar diretamente com a gente!</p>
            </div>
        </div>
        
        </div>
    </section>

    <!-- Seção Perguntas e respostas -->
    <section class="container-fluid bg-white py-5">
        <div id="perguntas_e_respotas" class="row">
            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto color1">
                <h3 class="fw-bold text-center">Perguntas e Respostas</h3>
                <p class="text-center lead">Ficou alguma dúvida? Clique nas perguntas abaixo.</p>
                <div class="mt-3 accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                Como funciona o curso
                            </button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">Após a confirmação do pagamento, você receberá por e-mail o acesso ao seu curso. O curso é totalmente online, composto por vídeo aulas acessíveis 24 horas por dia. Assim, você tem a liberdade de estudar quando e onde desejar.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                Quando começo a fazer o meu curso
                            </button>
                        </h2>
                        <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">O acesso ao curso é liberado após a confirmação do pagamento. Se o pagamento for feito por cartão de crédito, a liberação ocorre imediatamente. Você receberá um e-mail da HOTMART no próximo dia útil após o pagamento. Este e-mail pode estar na caixa de spam ou lixo eletrônico. Ao encontrá-lo, clique em "ACESSAR MEU PRODUTO" e crie uma senha para acessar o curso usando seu e-mail e a senha criada. Quando o pagamento for feito via PIX é necessário aguardar a confirmação do banco, que pode ser em alguns minutos ou em até 3 dias.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                                Quando posso fazer minha inscrição
                            </button>
                        </h2>
                        <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">Este valor promocional é limitado, e as inscrições podem ser encerradas a qualquer momento. Recomendamos que você faça sua inscrição o quanto antes para garantir sua vaga.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-heading04">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapse04" aria-expanded="false" aria-controls="flush-collapse04">
                                O certificado é reconhecido em todo o Brasil?
                            </button>
                        </h2>
                        <div id="flush-collapse04" class="accordion-collapse collapse" aria-labelledby="flush-heading04"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">SIM! Nossos cursos profissionalizantes, enquadrados como cursos livres, são autorizados a emitir certificados com base no Decreto N° 5.154, de 23 de Julho de 2004, Art. 1° e 3°, e de acordo com as normas do MEC pela Resolução CNE nº 04/99, Art 11º. Válidos em todo o território nacional, nossos certificados podem ser utilizados para enriquecer seu currículo e contar como horas extracurriculares em faculdades.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-heading05">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapse05" aria-expanded="false" aria-controls="flush-collapse05">
                                Como vou receber o certificado
                            </button>
                        </h2>
                        <div id="flush-collapse05" class="accordion-collapse collapse" aria-labelledby="flush-heading05"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">O certificado, em formato PDF, será disponibilizado ao final do curso para download e impressão. A parte frontal do certificado, contendo seu nome, é liberada após a conclusão da última aula. Exceção feita ao curso de Operador de Caixa, cujo certificado pode ser solicitado via WhatsApp, conforme informado no curso.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-heading06">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapse06" aria-expanded="false" aria-controls="flush-collapse06">
                                Este site é seguro?
                            </button>
                        </h2>
                        <div id="flush-collapse06" class="accordion-collapse collapse" aria-labelledby="flush-heading06"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">Sim! Nosso site é protegido por certificado de segurança, como indicado na URL. Além disso, o processamento de pagamentos é realizado por uma empresa especializada que garante a segurança do valor pago por 7 dias, permitindo o reembolso em caso de problemas com o curso. Nossa empresa, reconhecida e ativa há vários anos, tem uma forte presença nas redes sociais, evidenciando a realização de diversos projetos em todo o Brasil.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-heading07">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapse07" aria-expanded="false" aria-controls="flush-collapse07">
                                Há testes ou provas?
                            </button>
                        </h2>
                        <div id="flush-collapse07" class="accordion-collapse collapse" aria-labelledby="flush-heading07"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">Sim! Existem avaliações de recapitulação ao longo do curso. Não se preocupe, pois é possível refazer as avaliações mais de uma vez, se necessário.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-heading08">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapse08" aria-expanded="false" aria-controls="flush-collapse08">
                                Quais os requisitos para fazer o curso?
                            </button>
                        </h2>
                        <div id="flush-collapse08" class="accordion-collapse collapse" aria-labelledby="flush-heading08"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body"> Os cursos do Portal Jovem Empreendedor são acessíveis a pessoas de todas as idades e níveis de escolaridade. Mesmo para cursos em profissões que exigem ensino médio completo, é possível se matricular e iniciar o aprendizado enquanto conclui seus estudos.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>  
 
    
  
    <!-- Botão Fixo -->
    @if($desconto_banner) 
    <div id="promo-footer" class="promo-footer">
        @if($curso->origem!='whatsapp')
            <div class="promo-info">
                <p class="old-price my-0" style="font-size: small">de R${{$curso->preco_cheio}},00 por</p>
                <p class="new-price"> <span style="font-size: x-small">
                    @php
                        $preco = explode("R$", $curso->preco_parcelado_completo);
                    @endphp
                    {{$preco[0]}}R$</span>{{$preco[1]}}</p>
                <p class="my-0 color4" style="font-size: small">Termina em</p>
                <p class="countdown color4"></p>
            </div>
            <a href="{{$curso->link_checkout_completo}}" class="btn-cta my-0 fw-bold btn-inscricao">Fazer inscrição</a>
        @else
            <a href="{{$curso->link_checkout_completo}}@if( $curso->origem != 'whatsapp')&sck=btn_flutuante @endif" class="w-100 text-center btn-cta my-0 fw-bold btn-inscricao">Quero me inscrever agora </a>
        @endif
    </div>
    @endif

    <!-- Rodapé -->
    <footer id="rodape" class="text-white pt-5 bg-dark" >
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">
                <div class="row">
                    <!-- Logo -->
                    <div class="col-sm-4 mb-2 text-center text-md-start">
                        <img loading="lazy"  src="{{asset('img/home_page/logowhite.png')}}" alt="Logo" class=" img-fluid img-lazy mb-2" style="max-width: 150px;">
                        <!--<p style="font-size: small">Cnpj: 21.798.932/0001-00</p>-->
                    </div>

                    <!-- Redes Sociais -->
                    <div class="col-sm-4 mb-3 text-center">
                        <a href="https://www.instagram.com/jovemempreendedororg" target="_blank" class="text-white me-3" style="text-decoration: none;" aria-label="Siga-nos no Instagram">
                        <i class="bi bi-instagram" style="font-size: 1.5rem;"></i>
                        </a>
                        <a href="https://www.youtube.com/@PortalJovemEmpreendedor" target="_blank" class="text-white me-3"  style="text-decoration: none;" aria-label="Siga-nos no Youtube">
                        <i class="bi bi-youtube" style="font-size: 1.5rem;"></i>
                        </a>
                        <a href="https://www.facebook.com/portaljovemempreendedoroficial" target="_blank" class="text-white"  style="text-decoration: none;" aria-label="Siga-nos no Facebook">
                        <i class="bi bi-facebook" style="font-size: 1.5rem;"></i>
                        </a>
                    </div>

                    <!-- Links de Política -->
                    <div class="col-sm-4 text-center text-md-end">
                        <div class="text-center text-md-end">
                            <small>
                                <div class="row">
                                    <a target="_blanck" href="https://jovemempreendedor.org/afiliados/politica" class="politicas text-white me-3 col-sm-12">Política de Privacidade</a>
                                    <a  target="_blanck" href="https://jovemempreendedor.org/afiliados/termos" class="politicas text-white me-3 col-sm-12">Termos de Uso</a>
                                    <a  target="_blanck" href="https://jovemempreendedor.org/afiliados/termos" class="text-white col-sm-12">Uso de Cookies</a>
                                </div>
                                
                            </small>
                            </div>
                            
                        
                    </div>                    
                </div>
            </div>
            
          </div>
        </div>
    </footer>
      
    <!-- Modal com Formulário -->
    <div class="modal fade" id="inscricaoModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Complete sua Inscrição e Comece Sua Jornada de Sucesso!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Precisamos de algumas informações rápidas para garantir seu acesso ao curso. Isso nos ajuda a manter você atualizado sobre o conteúdo e certificação.</p>
                
                <!-- Formulário -->
                <form id="inscricaoForm" action="{{ route('lead_whatsapp') }}" method="POST">
                    <div class="mb-3">                    
                        <input type="text" class="form-control" id="input_lead_nome" name="buyer_name" placeholder="Digite seu nome completo" style="height: 50px;font-size: large;" required>
                        <div class="form-text mt-0 ms-2">Seu nome será impresso no certificado.</div>
                    </div>
                    
                    <!--Campos ocultos-->
                    <input id="input_lead_user_id" type="hidden" name="user_id" value="{{$curso->user_id ?? 13}}">
                    <input id="input_lead_curso_id" type="hidden" name="curso_id" value="{{$curso->id}}">
                    <input id="input_lead_origem" type="hidden" name="origem" value="{{$curso->origem ?? 'whatsapp'}}">

                    <div class="mb-3">    
                        <input minlength="13" id="input_lead_telefone" type="tel" name="buyer_checkout_phone" class="form-control input_telefone" placeholder="Digite apenas números" title="Digite seu número de telefone aqui" style="height: 50px;font-size: large;" required>
                        <div class="form-text mt-0 ms-2">Receba suporte e atualizações diretamente no WhatsApp.</div>
                    </div>
                    
                    <button type="submit" class="btn-cta w-100 text-uppercase">
                        @if($curso->origem!='whatsapp')
                        Finalizar Minha Inscrição
                        @else
                        <i class="bi bi-whatsapp"></i> Saiba mais no WhatsApp
                        @endif
                    </button>
                    <p class="text-center mt-3" style="font-size: small">Você será redirecionado para a <strong>página de pagamento da Hotmart.</strong> Todo o processo é <u>seguro</u> e seus dados estarão <u>protegidos por criptografia</u>.</p>
                    <div class="text-center">
                        <img src="{{asset('img/icons/pagamentos.webp')}}" alt="Portal Jovem Empreendedor" width="350" height="60" class="d-inline-block align-text-top img-lazy">
                    </div>
                    
                </form>
            </div>
            
            </div>
        </div>
    </div>
    
    <style>
.mx-auto {
  margin-right: auto !important;
  margin-left: auto !important;
}
.navbar-brand {
  padding-top: var(--bs-navbar-brand-padding-y);
  padding-bottom: var(--bs-navbar-brand-padding-y);
  margin-right: var(--bs-navbar-brand-margin-end);
  font-size: var(--bs-navbar-brand-font-size);
  color: var(--bs-navbar-brand-color);
  text-decoration: none;
  white-space: nowrap;
}
        .text-decoration-none{
            color: rgb(37, 37, 37)
        }

        .text-secondary {
        --bs-text-opacity: 1;
        color: rgba(var(--bs-secondary-rgb),var(--bs-text-opacity)) !important;
        }

        .nav-link{
            color: #4aa0ff;;
        }

        .ql-indent-1{
            font-size: small; color: #0056b3; padding-left: 5%;
        }

        #fundo_certificado {
            background-image: url("{{asset('img/padrao/fundo_certificado2.webp')}}"); /* Coloque a URL da imagem desejada */
            background-size: 500px; /* Tamanho original da imagem */
            background-repeat: repeat; /* Repetir tanto horizontalmente quanto verticalmente */
            background-position: center;
            position: relative;
        }

        
        #fundo_certificado .bg-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);  
            z-index: 0;
        }
       

        #rodape a{
            text-decoration: none;
        }

        #professor p{
            /*margin-top: 0;
            margin-bottom: 0;*/
        }

        ul {
        list-style-type: none; /* Remove os marcadores da lista */
        padding: 0;
        margin: 0;
        }

        ul li {
            padding: 8px 0; /* Espaçamento vertical entre os itens */
            border-bottom: 1px solid #e0e0e0; /* Linha fina abaixo de cada item */
        }

        ul li:last-child {
            border-bottom: none; /* Remove a linha do último item */
        }

        .promo-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .old-price {
            text-decoration: line-through;        
            color: #ff6666;
        }

        .new-price {
            font-size: 24px;
            font-weight: bold;
            color: #fff;
            margin-top: -10px;
            margin-bottom: 0;
        }

        .countdown {
            font-size: 18px;
            font-weight: bold;
            margin-top: -7px;
            margin-bottom: 0;
        }

        .video-facade {
            position: relative;
            aspect-ratio: 16 / 9; /* Garante a proporção 16:9 */
            width: 100%; /* Responsivo */
            overflow: hidden; /* Corta qualquer conteúdo que ultrapasse o contêiner */
            cursor: pointer;
        }

        .video-facade img {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            object-fit: cover; /* Faz com que a imagem seja cortada e mantenha a proporção */
            transform: translate(-50%, -50%);
        }

        .gradient-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
            z-index: 100;
        }

        .play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 101;
        }

        .video-facade h4 {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            z-index: 102;
            text-align: center;
        }


        .play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .play-button i {
            font-size: 30px;
            color: white;
        }

        .modal{
            z-index: 99999;
        }



        /**APENAS MOBILE**/
        @media (max-width: 768px) {
            
            .col-videos {
                width: 85%;
            }

            #rodape{
                padding-bottom: 150px;
            }

            .politicas{
                margin-bottom: 7px
            }

            .promo-footer.show {
                opacity: 1;
                visibility: visible;  
            }
        }

    </style>
    <script src="https://cdn.plyr.io/3.7.8/plyr.js" defer></script>

    <script>
        //MODAL COM A LISTA DAS AULAS
        function changeVideo(videoId) {
            // Obtém o elemento da facade
            const videoFacade = document.getElementById('modal_video_aula_demonstrativa');

            // Atualiza o data-video-id
            videoFacade.setAttribute('data-video-id', videoId);

            const thumbnail = `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
        
            // Garante que o iframe anterior seja removido
            videoFacade.innerHTML = `
                <img 
                    loading="lazy" 
                    src="${thumbnail}" 
                    alt="{{$curso->titulo}}" 
                    class="img-fluid img-lazy">
                <div class="play-button">
                    <i class="bi bi-play-fill"></i>
                </div>
            `;

            // Reanexa o evento de clique para o novo vídeo
            videoFacade.setAttribute('onclick', 'loadVideo(this)');
        }




        //FECHAR O MENU AO CLICAR EM UM ITEM
        function goToSection(event, sectionId) {
            event.preventDefault(); // Impede o comportamento padrão do link

            // Fecha o offcanvas
            const offcanvasElement = document.querySelector('.offcanvas');
            const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
            if (offcanvas) {
            offcanvas.hide(); // Fecha o menu offcanvas

            // Aguarda o offcanvas terminar de fechar antes de rolar
            offcanvasElement.addEventListener('hidden.bs.offcanvas', () => {
                const targetElement = document.getElementById(sectionId);
                if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth' }); // Faz scroll suave até a seção
                }
            }, { once: true }); // O evento será disparado apenas uma vez
            }
        }

        /**CRONOMETRO**/
        // Obter a data atual
        var today = new Date();

        // Ajustar a hora para 23:59:59 do dia atual
        today.setHours(23, 59, 59, 999);

        // Obter o timestamp da data ajustada
        var countDownDate = today.getTime();

        var countdownFunction = setInterval(function() {
            var now = new Date().getTime();
            var distance = countDownDate - now;

            // Cálculos para horas, minutos e segundos
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Seleciona todos os elementos com a classe 'countdown'
            var countdownElements = document.querySelectorAll('.countdown');

            // Itera sobre cada elemento e atualiza o conteúdo
            countdownElements.forEach(function(element) {
            element.innerHTML = hours + "<span style='font-size: small'>horas</span> " 
                                + minutes + "<span style='font-size: small'>min</span> " 
                                + seconds + "<span style='font-size: small'>seg</span>";
            });


            // Se a contagem terminar
            if (distance < 0) {
                clearInterval(countdownFunction);
                countdownElements.forEach(function(element) {
                element.innerHTML = "PROMOÇÃO ENCERRADA";
                });
            }
        }, 1000);

        /**BOTÃO DE COMPRA NO RODAPÉ DA PÁGINA**/
        @if($desconto_banner) 
        window.addEventListener('scroll', function() {
            var promoFooter = document.querySelector('.promo-footer');
            var windowHeight = window.innerHeight;
            var scrollPosition = window.scrollY;

            // Se a posição do scroll for maior que a altura da tela, mostra o bloco
            if (scrollPosition > windowHeight) {
                promoFooter.classList.add('show');
            } else {
                promoFooter.classList.remove('show');
            }
        });
        @endif

        

        /**ALTERAR LAYOUT DOS VIDEOS LADO A LADO**/
        function adjustClasses() {
            var videodepoimentos = document.getElementById('video-depoimentos');
            //var videodentro = document.getElementById('video_dentro_curso');
            if (window.innerWidth >= 768) { // Desktop breakpoint
                videodepoimentos.classList.remove('flex-nowrap', 'overflow-auto');
                //videodentro.classList.remove('flex-nowrap', 'overflow-auto');
            } else {
                videodepoimentos.classList.add('flex-nowrap', 'overflow-auto');
                //videodentro.classList.add('flex-nowrap', 'overflow-auto');
            }
        }

        // Run on page load
        window.addEventListener('load', adjustClasses);

        // Run on window resize
        window.addEventListener('resize', adjustClasses);
        
        
        function loadVideo(element) {
                if (element.querySelector('iframe')) {
                    return; // Se o iframe já existe, não faz nada
                }

            var videoId = element.getAttribute('data-video-id');

            // Substitui a facade pela incorporação do vídeo do YouTube com Plyr
            var playerHTML = `
                <div class="plyr__video-embed player">
                    <iframe
                        src="https://www.youtube.com/embed/${videoId}?autoplay=1&origin=${window.location.origin}&modestbranding=1&rel=0&iv_load_policy=3&playsinline=1&controls=1"
                        allow="autoplay; fullscreen"
                        allowfullscreen
                        frameborder="0"
                        style="width: 100%; height: 100%;">
                    </iframe>
                </div>
                <div style="position: absolute;top: 0;width: 100%;height: 70px;background-color: transparent;z-index: 101;left: 0;"></div>
            `;

            element.innerHTML = playerHTML;

            // Inicializa o Plyr para o vídeo carregado
            const player = new Plyr('.player', {
                controls: ['play', 'progress', 'current-time', 'mute', 'volume',],
                fullscreen: { enabled: false }
            });

            element.removeEventListener('click', loadVideo);
        }

        /* ####################################
         * APÓS CARREGAMENTO PARCIAL DA PÁGINA
         * ##################################*/ 
        document.addEventListener('DOMContentLoaded', function() {

            // Carregar Bootstrap CSS
            /*var bootstrapCSS = document.createElement('link');
            bootstrapCSS.rel = 'stylesheet';
            bootstrapCSS.href = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css';
            bootstrapCSS.integrity = 'sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH';
            bootstrapCSS.crossOrigin = 'anonymous';
            document.head.appendChild(bootstrapCSS);

            // ICONES DO BOOTSTRAP
            var bootstrapIconsCSS = document.createElement('link');
            bootstrapIconsCSS.rel = 'stylesheet';
            bootstrapIconsCSS.href = 'https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css';
            document.head.appendChild(bootstrapIconsCSS);*/
            
            // Carregar jquery
            var jquery = document.createElement('script');
            jquery.src = 'https://code.jquery.com/jquery-3.7.1.min.js';
            jquery.crossOrigin = 'anonymous';
            jquery.async = true;
            document.head.appendChild(jquery);

            /*document.querySelectorAll('img').forEach(function(img) {
                // Se a imagem não tiver atributos de largura e altura definidos
                if (!img.hasAttribute('width') || !img.hasAttribute('height')) {
                    // Defina as dimensões visuais da imagem no HTML (como está na tela)
                    img.setAttribute('width', img.offsetWidth);
                    img.setAttribute('height', img.offsetHeight);
                }
            });*/

            let observador = new IntersectionObserver((entradas) => {
            entradas.forEach(entrada => {
                if (entrada.isIntersecting) {
                    const img = entrada.target;
                    // Se você optar por substituir a fonte da imagem,
                    // certifique-se de que o atributo 'data-src' contém o caminho correto da imagem
                    // img.src = img.getAttribute('data-src');
                    img.removeAttribute('loading'); // Força o carregamento imediato
                    observador.unobserve(img); // Deixa de observar a imagem para melhorar a performance
                }
            });
            }, {
                rootMargin: "100px 0px", // Ajuste isso para carregar imagens antes de chegarem à viewport
                threshold: 0.01
            });

            const imagens = document.querySelectorAll('img.img-lazy');
            imagens.forEach(img => {
                observador.observe(img);
            });

            //CLASSE DOS BOTÕES DE COMPARTILHAR LINK
            const shareButtons = document.querySelectorAll('.share-button');
            shareButtons.forEach((button) => {
                button.addEventListener('click', async () => {
                    if (navigator.share) {
                    try {
                        await navigator.share({
                        title: '{{$curso->titulo}}',
                        text: '{{ $curso->headline }}',
                        url: window.location.href, // Você pode personalizar o URL com base no contexto do botão
                        });
                        console.log('Conteúdo compartilhado com sucesso');
                    } catch (error) {
                        console.error('Erro ao compartilhar:', error);
                    }
                    } else {
                    alert('A função de compartilhamento não é suportada neste navegador.');
                    }
                });
            });
        });

        /* ####################################
         * APÓS CARREGAMENTO COMPLETO DA PÁGINA
         * ##################################*/ 
        window.onload = function() {

            // Carregar Bootstrap CSS
            var bootstrapCSS = document.createElement('link');
            bootstrapCSS.rel = 'stylesheet';
            bootstrapCSS.href = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css';
            bootstrapCSS.integrity = 'sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH';
            bootstrapCSS.crossOrigin = 'anonymous';
            document.head.appendChild(bootstrapCSS);

            // Carregar Bootstrap JS
            var bootstrapJS = document.createElement('script');
            bootstrapJS.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js';
            bootstrapJS.integrity = 'sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz';
            bootstrapJS.crossOrigin = 'anonymous';
            bootstrapJS.async = true;
            document.head.appendChild(bootstrapJS);

            // Carregar Plyr CSS
            var plyrCSS = document.createElement('link');
            plyrCSS.rel = 'stylesheet';
            plyrCSS.href = 'https://cdn.plyr.io/3.7.8/plyr.css';
            document.head.appendChild(plyrCSS);
            
            // Carregar Plyr JS
            /*var plyrJS = document.createElement('script');
            plyrJS.src = 'https://cdn.plyr.io/3.7.8/plyr.js';
            plyrJS.async = true;
            document.head.appendChild(plyrJS);*/

            // Carregar inputmask
            var inputmask = document.createElement('script');
            inputmask.src = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js';
            document.head.appendChild(inputmask);

            // Máscara do formulario para o WhatsApp
            inputmask.onload = function() {            
                var telefoneInputs = document.querySelectorAll('.input_telefone');            
                // Aplica a máscara a cada campo de entrada
                telefoneInputs.forEach(function(input) {
                    Inputmask({
                        mask: ["(99) 9999-9999", "(99) 99999-9999"],
                        keepStatic: true
                    }).mask(input);
                });
            };  
            //MODAL FORMULÁRIO PRECHECKOUT E WHATSAPP
            //@if($curso->formulario)
            // Quando o Bootstrap JS for carregado, executa a função que usa o modal
            bootstrapJS.onload = function() {
                // Selecione todos os links com a classe btn-inscricao
                const links = document.querySelectorAll('.btn-inscricao');
                const modal = new bootstrap.Modal(document.getElementById('inscricaoModal'));

                links.forEach(link => {
                    link.addEventListener('click', function (event) {
                        // Previne o comportamento padrão (abrir o link)
                        event.preventDefault();

                        // Obtenha o href do link clicado
                        lastClickedButtonHref = $(this).attr('href');

                        // Altere o link de redirecionamento no botão de confirmação
                        //confirmButton.setAttribute('href', href);

                        // Abra o modal
                        modal.show();
                    });
                });
            };  
            //@endif

            $('#inscricaoForm').on('submit', function(event) {
                event.preventDefault();
                fbq('track', 'Lead');
                
                fbq('track', 'AddToCart', {
                    content_ids: ['{{$curso->id}}'], // 'REQUIRED': array of product IDs
                    content_type: 'product', // RECOMMENDED: Either product or product_group based on the content_ids or contents being passed.
                    });
                @if($curso->cidade) var cidade =  "{{$curso->cidade}}" @endif;
                var input_lead_nome = $('#input_lead_nome').val();
                var input_lead_user_id = $('#input_lead_user_id').val();
                var input_lead_curso_id = $('#input_lead_curso_id').val();                
                var input_lead_origem = $('#input_lead_origem').val();
                var input_lead_telefone = $('#input_lead_telefone').val();
                var telefoneApenasNumeros = input_lead_telefone.replace(/\D/g, '');

                if (telefoneApenasNumeros.length < 10) {
                    alert('O telefone deve conter no mínimo 10 dígitos.');
                    return;
                }

                var ddd = telefoneApenasNumeros.substring(0, 2);
                var telefone = telefoneApenasNumeros.substring(2);

                //var input_lead_link = $('.btn-inscricao').attr('href');
                var input_lead_link = lastClickedButtonHref;

                input_lead_link = input_lead_link.replace("{nome}", input_lead_nome); //COLOCAR O NOME DA PESSOA NA MENSAGEM
                window.location.href = input_lead_link+"&name="+input_lead_nome+"&phoneac="+ddd+"&phonenumber="+telefone;
                
                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: {
                        _token: '{{ csrf_token() }}',  // Certifique-se de incluir o CSRF token se necessário
                        nome: input_lead_nome,
                        curso_id: input_lead_curso_id,
                        user_id: input_lead_user_id,
                        origem: input_lead_origem,
                        telefone: telefoneApenasNumeros,
                        origem: input_lead_origem,
                        @if($curso->cidade) cidade: cidade @endif
                    },
                    success: function(response) {
                        console.log(response);
                    },
                    error: function(xhr, status, error) {
                        var errors = xhr.responseJSON.errors;
                        console.log(errors);
                    }
                });
            });

            /* Código base para dois Pixels do Facebook*/
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');

            // Inicializa o primeiro pixel
            fbq('init', '419961365827965'); // Substitua PIXEL_ID_1 pelo ID do primeiro Pixel
            // Inicializa o segundo pixel
            fbq('init', '948808649224691'); // Substitua PIXEL_ID_2 pelo ID do segundo Pixel

            @if($curso->meta_pixel_id AND $curso->meta_pixel_id!='948808649224691')
                fbq('init', '{{$curso->meta_pixel_id}}'); 
            @endif
            
            // Rastreia a visualização de página para ambos os pixels
            fbq('track', 'PageView'); 
            fbq('track', 'ViewContent', {
            content_ids: ['{{$curso->id}}'], // 'REQUIRED': array of product IDs
            content_type: 'product', // RECOMMENDED: Either product or product_group based on the content_ids or contents being passed.
            });
            
        };    
    </script>
    <script type="application/ld+json">
        {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "https://jovemempreendedor.org/{{$curso->url}}"
        },
        "headline": "{{$curso->titulo}}",
        "description": "{{$curso->headline}}",
        "image": "{{asset('/storage/'.$curso->capa_quadrada)}}",  
        "author": {
            "@type": "Person",
            "name": "{{$curso->professor_nome}}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "Portal Jovem Empreendedor",
            "logo": {
            "@type": "ImageObject",
            "url": "{{asset('img/home_page/logowhite.png')}}"
            }
        },
        "datePublished": "2024-10-24T08:00:00Z",
        "dateModified": "2024-10-24T09:00:00Z"
        }
    </script>
   
    <!-- Código base para dois Pixels do Facebook -->
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=419961365827965&ev=PageView&noscript=1"
    /></noscript>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=948808649224691&ev=PageView&noscript=1"
    /></noscript>
    @if($curso->meta_pixel_id AND $curso->meta_pixel_id!='948808649224691')
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{$curso->meta_pixel_id}}&ev=PageView&noscript=1"
    /></noscript> 
    @endif
    <!-- Fim do Código base para dois Pixels do Facebook -->

</body>
</html>
