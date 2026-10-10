<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Image intelligence · {{ config('app.name', 'VisionFusion AI') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f4f6fb] font-sans text-slate-900 antialiased">
    <div class="min-h-screen overflow-hidden">
        <header class="border-b border-slate-200/80 bg-white/90">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 sm:px-8">
                <a href="{{ route('images.index') }}" class="flex items-center gap-3" aria-label="VisionFusion AI home">
                    <span class="grid size-10 place-items-center rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-600/20">
                        <svg viewBox="0 0 24 24" fill="none" class="size-6" aria-hidden="true">
                            <path d="M3 12s3.2-6 9-6 9 6 9 6-3.2 6-9 6-9-6-9-6Z" stroke="currentColor" stroke-width="1.7"/>
                            <circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.7"/>
                            <path d="m18.3 4.5.8-1.5M5.7 4.5 4.9 3M20.5 18.5l.8 1.5M3.5 18.5l-.8 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span>
                        <span class="block text-base font-bold tracking-tight">VisionFusion <span class="text-indigo-600">AI</span></span>
                        <span class="block text-[11px] font-medium uppercase tracking-[0.16em] text-slate-400">Multimodal image intelligence</span>
                    </span>
                </a>
                <div class="flex items-center gap-5 text-sm font-medium">
                    <a class="hidden text-slate-500 transition hover:text-indigo-600 sm:block" href="#how-it-works">How it works</a>
                    <a class="rounded-full bg-slate-100 px-3.5 py-2 text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700" href="#upload">Analyze an image</a>
                </div>
            </nav>
        </header>

        <main id="top" class="mx-auto max-w-7xl px-5 pb-16 pt-10 sm:px-8 sm:pt-16">
            <section class="grid items-center gap-10 lg:grid-cols-[1fr_0.82fr] lg:gap-16">
                <div class="max-w-2xl">
                    <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-white px-3 py-1.5 text-xs font-semibold text-indigo-700 shadow-sm">
                        <span class="size-1.5 rounded-full bg-emerald-500"></span>
                        Your image, ready for analysis
                    </div>
                    <h1 class="text-4xl font-semibold leading-[1.1] tracking-tight text-slate-950 sm:text-5xl lg:text-[3.5rem]">
                        See more in every <span class="text-indigo-600">image.</span>
                    </h1>
                    <p class="mt-5 max-w-xl text-base leading-7 text-slate-600 sm:text-lg">
                        Upload an image to validate it and inspect its details. VisionFusion keeps your image temporary and never stores it in this step.
                    </p>
                    <div id="how-it-works" class="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-sm text-slate-500">
                        <span class="inline-flex items-center gap-2"><span class="grid size-5 place-items-center rounded-full bg-indigo-100 text-[11px] font-bold text-indigo-700">1</span> Choose an image</span>
                        <span class="inline-flex items-center gap-2"><span class="grid size-5 place-items-center rounded-full bg-indigo-100 text-[11px] font-bold text-indigo-700">2</span> Validate securely</span>
                        <span class="inline-flex items-center gap-2"><span class="grid size-5 place-items-center rounded-full bg-indigo-100 text-[11px] font-bold text-indigo-700">3</span> View image details</span>
                    </div>
                </div>

                <div class="relative mx-auto hidden w-full max-w-md lg:block" aria-hidden="true">
                    <div class="absolute -inset-6 rounded-[2.5rem] bg-gradient-to-br from-indigo-100 via-white to-cyan-100 blur-2xl"></div>
                    <div class="relative rounded-[2rem] border border-white bg-white p-4 shadow-[0_24px_80px_-32px_rgba(51,65,125,0.35)]">
                        <div class="relative overflow-hidden rounded-[1.4rem] bg-gradient-to-br from-indigo-950 via-indigo-800 to-cyan-700 p-8">
                            <div class="absolute -right-12 -top-16 size-52 rounded-full border border-white/15"></div>
                            <div class="absolute -right-2 -top-6 size-32 rounded-full border border-white/15"></div>
                            <div class="relative flex aspect-[1.55] items-center justify-center">
                                <svg viewBox="0 0 260 170" fill="none" class="w-full max-w-sm">
                                    <rect x="23" y="20" width="214" height="130" rx="18" fill="white" fill-opacity=".08" stroke="white" stroke-opacity=".28"/>
                                    <circle cx="83" cy="64" r="13" fill="#FCD34D"/>
                                    <path d="m42 128 51-48 33 30 27-23 65 41H42Z" fill="#A5F3FC" fill-opacity=".85"/>
                                    <path d="M112 99h51M112 108h37M112 117h44" stroke="white" stroke-opacity=".8" stroke-width="4" stroke-linecap="round"/>
                                    <rect x="103" y="87" width="65" height="39" rx="5" stroke="#C4B5FD" stroke-width="2" stroke-dasharray="4 4"/>
                                    <circle cx="201" cy="44" r="15" fill="#34D399"/>
                                    <path d="m194 44 5 5 9-10" stroke="#064E3B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="absolute bottom-5 left-5 rounded-xl border border-white/15 bg-white/10 px-3 py-2 text-xs font-medium text-white backdrop-blur">
                                <span class="mr-1.5 text-emerald-300">●</span> Ready for analysis
                            </div>
                        </div>
                        <div class="flex items-center justify-between px-3 pb-1 pt-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Image intelligence</p>
                                <p class="mt-1 text-xs text-slate-400">A clearer view, one upload away</p>
                            </div>
                            <span class="rounded-lg bg-indigo-50 px-2.5 py-1.5 text-xs font-semibold text-indigo-700">VISION AI</span>
                        </div>
                    </div>
                </div>
            </section>

            <section id="upload" class="mx-auto mt-12 grid max-w-5xl gap-5 lg:mt-16 lg:grid-cols-[1fr_300px]">
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_12px_45px_-30px_rgba(15,23,42,0.25)] sm:p-8">
                    <div class="mb-6 flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Image analysis</p>
                            <h2 class="mt-1.5 text-xl font-semibold tracking-tight text-slate-900">Upload your image</h2>
                            <p class="mt-1.5 text-sm text-slate-500">Start with one clear image. We’ll check the format and dimensions.</p>
                        </div>
                        <span class="hidden rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 sm:inline-flex">Private by design</span>
                    </div>

                    <div id="upload-error" class="mb-4 hidden rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert" aria-live="polite"></div>

                    @if ($errors->has('image'))
                        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
                            {{ $errors->first('image') }}
                        </div>
                    @endif

                    <form action="{{ route('images.analyze') }}" method="POST" enctype="multipart/form-data" data-upload-form data-max-bytes="{{ config('image_upload.max_size_kb') * 1024 }}" data-max-size-mb="{{ config('image_upload.max_size_mb') }}">
                        @csrf
                        <input id="image-input" name="image" type="file" class="sr-only" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" aria-label="Choose an image">

                        <div id="drop-zone" class="group relative flex min-h-56 cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/80 px-5 py-8 text-center transition hover:border-indigo-300 hover:bg-indigo-50/40 focus-within:border-indigo-400 sm:min-h-64">
                            <div id="empty-state" class="flex flex-col items-center">
                                <span class="mb-4 grid size-14 place-items-center rounded-2xl bg-white text-indigo-600 shadow-sm ring-1 ring-slate-200/80 transition group-hover:scale-105">
                                    <svg viewBox="0 0 24 24" fill="none" class="size-7" aria-hidden="true">
                                        <path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <p class="text-sm font-semibold text-slate-800">Drop your image here</p>
                                <p class="mt-1 text-sm text-slate-500">or <span class="font-semibold text-indigo-600">browse files</span></p>
                                <p class="mt-3 text-xs text-slate-400">JPG, JPEG, PNG or WEBP · Up to {{ config('image_upload.max_size_mb') }} MB</p>
                            </div>
                            <div id="preview-state" class="hidden w-full max-w-sm">
                                <img id="image-preview" class="mx-auto max-h-40 rounded-xl object-contain shadow-sm" alt="Selected image preview">
                                <p id="preview-filename" class="mt-3 truncate text-sm font-semibold text-slate-800"></p>
                                <p id="preview-size" class="mt-1 text-xs text-slate-500"></p>
                            </div>
                            <button id="browse-button" type="button" class="absolute inset-0 z-10 cursor-pointer rounded-2xl focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" aria-label="Browse for an image"></button>
                        </div>

                        <div class="mt-5 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-xs leading-5 text-slate-400">Your image is sent securely for validation and is not stored.</p>
                            <button id="submit-button" type="submit" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-wait disabled:opacity-70">
                                <svg id="submit-spinner" class="hidden size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"/>
                                </svg>
                                <span id="submit-label">Upload and Analyze</span>
                                <svg id="submit-arrow" viewBox="0 0 20 20" fill="none" class="size-4" aria-hidden="true">
                                    <path d="M4 10h12m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <aside class="flex flex-col gap-4">
                    <div class="rounded-3xl border border-indigo-100 bg-gradient-to-br from-indigo-600 to-indigo-800 p-6 text-white shadow-lg shadow-indigo-900/10">
                        <span class="grid size-10 place-items-center rounded-xl bg-white/15">
                            <svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true">
                                <path d="M12 3 5 6v5c0 4.5 3 8.2 7 10 4-1.8 7-5.5 7-10V6l-7-3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <h3 class="mt-5 text-base font-semibold">Built for safe uploads</h3>
                        <p class="mt-2 text-sm leading-6 text-indigo-100">Images are checked on the server and passed to the AI service only for this request.</p>
                        <div class="mt-5 border-t border-white/15 pt-4 text-xs font-medium text-indigo-100">No permanent image storage</div>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white p-6">
                        <h3 class="text-sm font-semibold text-slate-800">Supported formats</h3>
                        <div class="mt-4 grid grid-cols-4 gap-2">
                            @foreach (['JPG', 'JPEG', 'PNG', 'WEBP'] as $format)
                                <span class="rounded-lg bg-slate-50 px-2 py-2 text-center text-[11px] font-bold text-slate-500 ring-1 ring-slate-100">{{ $format }}</span>
                            @endforeach
                        </div>
                        <p class="mt-4 text-xs leading-5 text-slate-400">Maximum file size: {{ config('image_upload.max_size_mb') }} MB</p>
                    </div>
                </aside>
            </section>

            <section id="analysis-result" class="{{ session()->has('analysis') ? '' : 'hidden' }} mx-auto mt-6 max-w-5xl rounded-3xl border border-emerald-200 bg-white p-5 shadow-sm sm:p-8" aria-live="polite">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">Validation complete</p>
                        <h2 class="mt-1.5 text-xl font-semibold text-slate-900">Image details</h2>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">Image accepted</span>
                </div>
                <p id="result-message" class="mt-3 text-sm leading-6 text-slate-600">{{ session('analysis.message') }}</p>
                <dl class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach (['filename' => 'File name', 'format' => 'Format', 'dimensions' => 'Dimensions', 'file_size_bytes' => 'File size'] as $key => $label)
                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-xs font-medium text-slate-400">{{ $label }}</dt>
                            <dd id="result-{{ $key }}" class="mt-1.5 break-all text-sm font-semibold text-slate-800">
                                @if (session()->has('analysis'))
                                    @if ($key === 'dimensions')
                                        {{ session('analysis.width') }} × {{ session('analysis.height') }} px
                                    @elseif ($key === 'file_size_bytes')
                                        {{ number_format(session('analysis.file_size_bytes') / 1024, 1) }} KB
                                    @else
                                        {{ session('analysis.'.$key) }}
                                    @endif
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
                <button id="upload-another" type="button" class="mt-6 inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                    <svg viewBox="0 0 20 20" fill="none" class="size-4" aria-hidden="true">
                        <path d="M4 10a6 6 0 0 1 10.2-4.3L16 7.5M16 4v3.5h-3.5M16 10a6 6 0 0 1-10.2 4.3L4 12.5M4 16v-3.5h3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Upload another image
                </button>
            </section>

            <footer class="mx-auto mt-12 flex max-w-5xl flex-col justify-between gap-2 border-t border-slate-200/80 pt-5 text-xs text-slate-400 sm:flex-row">
                <span>© {{ date('Y') }} VisionFusion AI</span>
                <span>Image intelligence, thoughtfully built.</span>
            </footer>
        </main>
    </div>
</body>
</html>
