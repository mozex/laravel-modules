---
title: Inertia
weight: 4
---

Laravel Modules only manages PHP-side discovery. Your frontend files (Vue or React components, TypeScript, CSS) aren't touched by the package. But if you're building an Inertia.js frontend, you probably want each module to own its own pages, components, and styles. This page covers the conventions and tooling that make that work.

Nothing on this page is required by the package. Adopt what's useful, skip what isn't.

These patterns work with Vue or React, whichever Inertia adapter you're using, and don't depend on specific Inertia, Vite, Vue, or React versions. Inertia's official Vite plugin auto-resolves pages from `./Pages/`, but since module-scoped pages live outside that directory, the manual `resolve` callback shown below is still the right tool. Inertia's docs explicitly support the manual callback as an alternative to the auto-resolve plugin.

## Directory convention

Each module gets a `Resources/` directory for frontend assets alongside its PHP code. A typical module layout with Vue:

```
Modules/Blog/
├── Http/Controllers/
├── Models/
├── Routes/
└── Resources/
    ├── css/
    │   └── blog.css
    ├── views/           (Blade views, managed by the package)
    └── ts/
        ├── Pages/       (Inertia pages for this module)
        │   ├── Post/
        │   │   ├── Index.vue
        │   │   └── Show.vue
        │   └── Home.vue
        ├── Layouts/     (page layouts)
        ├── Partials/    (page-scoped child components)
        ├── Components/  (shared components from this module)
        ├── Composable/  (Vue composables)
        ├── Icons/       (icon components)
        ├── Interfaces/  (TypeScript interfaces)
        └── Stores/      (Pinia stores)
```

For React, the structure is identical but files are `.tsx` and the `Composable/` directory becomes `Hooks/`:

```
Modules/Blog/Resources/ts/
├── Pages/
│   ├── Post/
│   │   ├── Index.tsx
│   │   └── Show.tsx
│   └── Home.tsx
├── Layouts/
├── Components/
├── Hooks/       (React hooks: useX.ts)
├── Icons/
├── Interfaces/
└── Stores/      (Zustand/Redux stores)
```

The package discovers `Resources/views/` for Blade templates. Everything else under `Resources/` (`ts/`, `css/`, `images/`, `fonts/`) is wired up by Vite and your frontend tooling, not by the package itself.

## Vite alias

Add a regex-based alias to `vite.config.ts` so any import starting with `@Modules/{Name}/` resolves into that module's `Resources/ts/` directory. This is the same config for Vue and React, only the framework plugin changes:

```ts
// Vue
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/ts/app.ts', 'resources/css/app.css'],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: [
            { find: /^@\//, replacement: '/resources/ts/' },
            { find: /^@Modules\/([^\/]+)\/(.*)$/, replacement: '/Modules/$1/Resources/ts/$2' },
        ],
    },
});
```

```ts
// React
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/ts/app.tsx', 'resources/css/app.css'],
            refresh: true,
        }),
        react(),
    ],
    resolve: {
        alias: [
            { find: /^@\//, replacement: '/resources/ts/' },
            { find: /^@Modules\/([^\/]+)\/(.*)$/, replacement: '/Modules/$1/Resources/ts/$2' },
        ],
    },
});
```

The regex approach matters because adding a new module to your project shouldn't require editing the Vite config. A single pattern covers every module forever. Without it, you'd need a per-module alias entry every time.

With this in place, components can import across modules using the same syntax regardless of which module they live in:

```ts
// Vue
import ForumLayout from '@Modules/Forum/Layouts/ForumLayout.vue';
import Button from '@Modules/Shared/Components/Button.vue';
import useReplyActions from '@Modules/Forum/Composable/useReplyActions';

// React
import ForumLayout from '@Modules/Forum/Layouts/ForumLayout';
import Button from '@Modules/Shared/Components/Button';
import useReplyActions from '@Modules/Forum/Hooks/useReplyActions';
```

## Inertia page resolver

Inertia's default resolver only looks in one pages directory. For module-scoped pages, extend the resolver to handle the `@Modules/` prefix. The logic is identical for both frameworks; only the Inertia adapter package and the file extension in the glob patterns change.

### Vue

```ts
// resources/ts/app.ts
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

type Glob = Record<string, () => Promise<DefineComponent>>;

const appPages = import.meta.glob<DefineComponent>('./Pages/**/*.vue');
const modulePages = import.meta.glob<DefineComponent>('../../Modules/**/Resources/ts/Pages/**/*.vue');

function resolveInertiaPage(name: string): [string, Glob] {
    if (name.startsWith('@Modules/')) {
        const withoutPrefix = name.replace('@Modules/', '');
        const module = withoutPrefix.substring(0, withoutPrefix.indexOf('/'));
        const pagePath = withoutPrefix.slice(module.length + 1);

        return [
            `../../Modules/${module}/Resources/ts/Pages/${pagePath}.vue`,
            modulePages,
        ];
    }

    return [`./Pages/${name}.vue`, appPages];
}

createInertiaApp({
    resolve: (name) => resolvePageComponent(...resolveInertiaPage(name)),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) }).use(plugin).mount(el);
    },
});
```

### React

```tsx
// resources/ts/app.tsx
import type { ResolvedComponent } from '@inertiajs/react';
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

type Glob = Record<string, () => Promise<ResolvedComponent>>;

const appPages = import.meta.glob<ResolvedComponent>('./Pages/**/*.tsx');
const modulePages = import.meta.glob<ResolvedComponent>('../../Modules/**/Resources/ts/Pages/**/*.tsx');

function resolveInertiaPage(name: string): [string, Glob] {
    if (name.startsWith('@Modules/')) {
        const withoutPrefix = name.replace('@Modules/', '');
        const module = withoutPrefix.substring(0, withoutPrefix.indexOf('/'));
        const pagePath = withoutPrefix.slice(module.length + 1);

        return [
            `../../Modules/${module}/Resources/ts/Pages/${pagePath}.tsx`,
            modulePages,
        ];
    }

    return [`./Pages/${name}.tsx`, appPages];
}

createInertiaApp({
    resolve: (name) => resolvePageComponent(...resolveInertiaPage(name)),
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
```

Two `import.meta.glob` calls tell Vite to include all matching files in the bundle. The resolver picks the right glob based on the name prefix. Application pages still live in `resources/ts/Pages/` and are referenced by their normal name. Module pages are referenced by `@Modules/{Module}/{Path}`.

## Controller usage

Controllers are PHP, so this part is framework-agnostic. With the resolver in place, controllers return module page names using the `@Modules/` prefix:

```php
namespace Modules\Blog\Http\Controllers;

use Inertia\Response;
use Modules\Blog\Data\PostShowResource;
use Modules\Blog\Models\Post;

class PostsController
{
    public function show(Post $post): Response
    {
        return inertia(
            component: '@Modules/Blog/Post/Show',
            props: PostShowResource::from($post),
        );
    }
}
```

The component name `@Modules/Blog/Post/Show` tells the frontend resolver to load `Modules/Blog/Resources/ts/Pages/Post/Show.vue` (Vue) or `.tsx` (React). The prefix segment (`@Modules/`) is the convention you defined in the resolver; the second segment (`Blog`) is the module directory; the rest is the page path inside the module's `Pages/` directory.

For inline Inertia routes (routes that render a page without a controller), the same pattern works:

```php
Route::inertia('/coming-soon', '@Modules/Shared/ComingSoon')->name('coming-soon');
```

## TypeScript configuration

The `tsconfig.json` setup is identical for Vue and React. Vite's regex alias handles the build. TypeScript needs its own paths configuration so the IDE can resolve imports and type-check correctly. The catch: TypeScript doesn't support regex in `paths`, so you need one entry per module.

```json
{
    "compilerOptions": {
        "baseUrl": ".",
        "paths": {
            "@/*": ["./resources/ts/*"],
            "@Modules/Blog/*":   ["./Modules/Blog/Resources/ts/*"],
            "@Modules/Forum/*":  ["./Modules/Forum/Resources/ts/*"],
            "@Modules/Shared/*": ["./Modules/Shared/Resources/ts/*"],
            "@Modules/User/*":   ["./Modules/User/Resources/ts/*"]
        }
    },
    "include": [
        "resources/ts/**/*.ts",
        "resources/ts/**/*.d.ts",
        "resources/ts/**/*.vue",
        "resources/ts/**/*.tsx",
        "Modules/*/Resources/ts/**/*.ts",
        "Modules/*/Resources/ts/**/*.d.ts",
        "Modules/*/Resources/ts/**/*.vue",
        "Modules/*/Resources/ts/**/*.tsx"
    ]
}
```

The `include` section uses globs, so it picks up every module's TS files without per-module entries. Only the `paths` section needs an entry per module. Add a new entry whenever you create a module that has a frontend.

Keep only the extensions you actually use (`.vue` if you're on Vue, `.tsx` if you're on React). If you use plain JavaScript instead of TypeScript, `jsconfig.json` takes the same shape with the same `paths` entries.

## Typed props via Spatie TypeScript Transformer

Pair [spatie/laravel-typescript-transformer](https://github.com/spatie/laravel-typescript-transformer) with [spatie/laravel-data](https://github.com/spatie/laravel-data) to generate TypeScript types directly from your PHP resource classes. The PHP-side setup is the same for both frameworks. Configure auto-discovery to include modules:

```php
// config/typescript-transformer.php
return [
    'auto_discover_types' => [
        app_path(),
        base_path('Modules/*'),
    ],
    // ...
];
```

Running `php artisan typescript:transform` then generates a type definitions file (typically `resources/ts/types/backend.d.ts`) containing one interface per Data/Resource class across all modules.

The frontend side of making those types globally available differs between Vue and React.

### Vue

Vue SFC compiler macros like `defineProps<T>()` run at compile time inside `@vitejs/plugin-vue`, so they don't automatically see TypeScript interfaces declared in arbitrary `.d.ts` files. Register the file as a global type file on the Vue plugin. `script.globalTypeFiles` is an option that's been available since Vue 3.3, added specifically to let `defineProps<GlobalType>()` resolve globally declared interfaces:

```ts
vue({
    script: {
        globalTypeFiles: ['resources/ts/types/backend.d.ts'],
    },
}),
```

Now your Vue pages can type their props directly against PHP classes without any import:

```vue
<script lang="ts" setup>
// PostShowResource is defined in PHP (Modules\Blog\Data\PostShowResource)
// and globally declared in backend.d.ts
const props = defineProps<PostShowResource>();
</script>
```

### React

React uses plain TypeScript with no SFC compiler, so there's no equivalent to `globalTypeFiles`. Any `declare interface` in a `.d.ts` file that's part of `tsconfig.json`'s `include` array becomes globally available automatically. No Vite plugin configuration needed.

Make sure your tsconfig includes the generated types file (it will already be covered by the `resources/ts/**/*.d.ts` glob in the example above, but if you output elsewhere, add an explicit entry):

```json
"include": [
    "resources/ts/**/*.d.ts",
    "resources/ts/types/backend.d.ts"
]
```

Then type your page component's props by destructuring them against the generated interface:

```tsx
// Modules/Blog/Resources/ts/Pages/Post/Show.tsx
export default function Show({ post, replies }: PostShowResource) {
    return (
        <article>
            <h1>{post.title}</h1>
            {/* ... */}
        </article>
    );
}
```

This gives you type-safe access to the props the controller returns via `PostShowResource::from($post)`. For access to shared props (flash messages, auth user, errors, and anything your middleware adds) along with page-specific props, use Inertia's `usePage` hook with a generic instead:

```tsx
import { usePage } from '@inertiajs/react';

export default function Show() {
    const { props } = usePage<PostShowResource>();

    return <article><h1>{props.post.title}</h1></article>;
}
```

Both patterns are shown in Inertia's official TypeScript guide. Pick destructured function parameters when you only need the page-specific props. Reach for `usePage<T>()` when you also need shared props or Inertia page metadata (`url`, `component`, `version`).

Change a field on the PHP side, regenerate the types, and TypeScript catches any component that breaks. End-to-end type safety across the Laravel-Inertia stack, regardless of whether you're on Vue or React.

## Module CSS

Module stylesheets can be imported into your main `resources/css/app.css` using the Vite alias. If you adjust the alias to point at `Resources/` instead of `Resources/ts/`, the imports stay clean:

```ts
// vite.config.ts
{ find: /^@Modules\/([^\/]+)\/(.*)$/, replacement: '/Modules/$1/Resources/$2' },
```

Then in your main CSS:

```css
@import '@Modules/Forum/css/forum.css';
@import '@Modules/Blog/css/blog.css';
```

The tradeoff: with this broader alias, component imports become `@Modules/Forum/ts/Components/X.vue` instead of `@Modules/Forum/Components/X.vue`. Pick whichever shape you prefer and stay consistent. If you want the shorter TS paths, keep the alias pointing at `Resources/ts/` and import CSS with a relative path, or add a second alias specifically for CSS.

## What the package does and doesn't do

The package discovers and registers PHP-side assets: configs, routes, views (Blade), migrations, service providers, commands, translations, Livewire components, Filament resources, and so on. None of the frontend conventions on this page are enforced or configured by the package. You're wiring them up yourself via Vite, TypeScript, and your Inertia bootstrap code.

That's intentional. Frontend tooling changes faster than Laravel. Keeping the package focused on PHP discovery means you can pick any Vite, Inertia, TypeScript, Vue, or React setup that suits your project without fighting the package's opinions.
