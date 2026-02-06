# Filament

## Overview

Auto-discovers Filament Resources, Pages, Widgets, and Clusters within modules and registers them per panel. The panel id is inferred from the directory name under `Filament/`.

## What gets discovered

Four independently toggleable features (when Filament is installed):
- Resources: `*/Filament/*/Resources`
- Pages: `*/Filament/*/Pages`
- Widgets: `*/Filament/*/Widgets`
- Clusters: `*/Filament/*/Clusters`

The second `*` in each pattern is the panel name. It's lowercased to form the panel id (e.g., `Admin` → `admin`).

## Default configuration

```php
'filament-resources' => [
    'active' => true,
    'patterns' => ['*/Filament/*/Resources'],
],
'filament-pages' => [
    'active' => true,
    'patterns' => ['*/Filament/*/Pages'],
],
'filament-widgets' => [
    'active' => true,
    'patterns' => ['*/Filament/*/Widgets'],
],
'filament-clusters' => [
    'active' => true,
    'patterns' => ['*/Filament/*/Clusters'],
],
```

## How panel mapping works

- The panel name is extracted from the directory path: `Filament/{Panel}/Resources`.
- During registration, each Filament panel is iterated and only assets matching that panel id are registered via `$panel->discoverResources()`, `discoverPages()`, `discoverWidgets()`, `discoverClusters()`.
- Module Livewire components registered by Filament are re-registered with Livewire to ensure correct aliases.

## Directory layout

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
        └── Clusters/
            └── NestedPost.php
```

## Usage

- Place Filament classes under `Modules/{Module}/Filament/{PanelId}/...` following Filament conventions.
- Ensure your app has panel providers with matching ids (e.g., `AdminPanelProvider` with id `admin`).

## Configuration

- Disable any feature by setting `'active' => false` in its section.
- Adjust glob patterns to match your directory structure.

## Troubleshooting

- **Not appearing in panel**: confirm directory has the panel segment (e.g., `Filament/Admin/Resources`) and your app defines a panel with matching id.
- **Namespace mismatch**: class namespaces must match PSR-4 paths.
