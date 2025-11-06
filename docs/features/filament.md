# Filament

## Overview

The package auto-discovers Filament Resources, Pages, Widgets, and Clusters within your modules and registers them per panel. It respects your Filament panel providers and maps each module’s Filament classes to the correct panel based on directory naming.

## What gets discovered

- When Filament is installed and enabled, the following features can be toggled independently:
  - Resources: `*/Filament/*/Resources`
  - Pages: `*/Filament/*/Pages`
  - Widgets: `*/Filament/*/Widgets`
  - Clusters: `*/Filament/*/Clusters`
- The `*` segment denotes the panel name (e.g., `Admin`, `Dashboard`). The panel id is the kebab-case of that segment (e.g., `Admin` → `admin`).
- Each discovered directory is paired with its PSR-4 namespace and panel id, then handed to Filament’s `discover*()` APIs.

## Default configuration

In `config/modules.php`:

```php
'filament-resources' => [
    'active' => true,
    'patterns' => [
        '*/Filament/*/Resources',
    ],
],

'filament-pages' => [
    'active' => true,
    'patterns' => [
        '*/Filament/*/Pages',
    ],
],

'filament-widgets' => [
    'active' => true,
    'patterns' => [
        '*/Filament/*/Widgets',
    ],
],

'filament-clusters' => [
    'active' => true,
    'patterns' => [
        '*/Filament/*/Clusters',
    ],
],
```

## How panel mapping works

- The panel name is extracted from the second wildcard in the pattern: `*/Filament/*/...`.
- It’s converted to lowercase for the final id (e.g., `Admin` or `admin` → `admin`).
- During boot, each Filament panel is iterated and only the assets matching that panel id are registered via:
  - `$panel->discoverResources(in: $path, for: $namespace)`
  - `$panel->discoverPages(in: $path, for: $namespace)`
  - `$panel->discoverWidgets(in: $path, for: $namespace)`
  - `$panel->discoverClusters(in: $path, for: $namespace)`
- Any panel-contained Livewire components that use module namespaces are re-registered with Livewire to ensure the correct component aliases are available.

## Directory layout examples

```
Modules/Blog/
└── Filament/
    ├── Admin/
    │   ├── Resources/
    │   │   └── PostResource.php
    │   ├── Pages/
    │   │   └── SettingsPage.php
    │   └── Widgets/
    │       └── PostOverviewWidget.php
    └── Dashboard/
        ├── Resources/
        │   └── NestedPostResource.php
        ├── Pages/
        │   └── CreatePostPage.php
        └── Clusters/
            └── NestedPost.php
```

## Usage

- Define Filament classes under module directories per panel, following Filament’s conventions. The panel id is inferred from the
  directory name.
- Ensure your app registers panels (e.g., AdminPanelProvider, DashboardPanelProvider) with ids matching the inferred ids (e.g., `admin`, `dashboard`).
- The package will discover and register your module’s Filament classes into the corresponding panels at boot.

## Configuration options

- Toggle features
  - Disable any of the four features by setting `'active' => false` in its section.
- Change discovery patterns
  - Adjust the glob patterns to match your desired directory structure under each module.

## Troubleshooting

- Not appearing in a panel: confirm the directory includes the panel segment (e.g., `Filament/Admin/Resources`) and that your app defines a panel with the matching id (`admin`).
- Namespace mismatch: ensure class namespaces match their PSR‑4 paths under the module.

## See also

- [Livewire Components](./livewire-components.md)
- [Views](./views.md)
- [Routes](./routes.md)
