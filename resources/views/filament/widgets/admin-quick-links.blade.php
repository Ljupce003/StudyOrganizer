@php
    use App\Filament\Resources\Courses\CourseResource;
    use App\Filament\Resources\Users\UserResource;
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <div id="quick-links-root" style="display:flex; gap:16px; flex-wrap:wrap; align-items:stretch;">

            {{-- COURSES --}}
            <div data-card
                 style="flex:1 1 340px; border:1px solid rgba(0,0,0,0.12); border-radius:14px; padding:18px; background:#fff; box-shadow:0 1px 2px rgba(0,0,0,0.06); transition: box-shadow 150ms ease, transform 150ms ease;"
                 onmouseover="this.style.boxShadow = (window.__ql_isDark ? '0 10px 24px rgba(0,0,0,0.55)' : '0 10px 24px rgba(0,0,0,0.10)'); this.style.transform='translateY(-2px)';"
                 onmouseout="this.style.boxShadow = (window.__ql_isDark ? '0 1px 2px rgba(0,0,0,0.35)' : '0 1px 2px rgba(0,0,0,0.06)'); this.style.transform='translateY(0)';"
            >
                <div style="display:flex; gap:12px; align-items:flex-start;">
                    <div data-badge style="width:36px; height:36px; border-radius:10px; background:rgba(0,0,0,0.04); display:flex; align-items:center; justify-content:center;">
                        <x-filament::icon icon="heroicon-o-rectangle-stack" style="width:22px; height:22px;" />
                    </div>

                    <div style="flex:1;">
                        <div data-title style="font-size:18px; font-weight:700; line-height:1.2; margin:0; color:#0f172a;">
                            Courses
                        </div>
                        <div data-desc style="margin-top:6px; font-size:13px; line-height:1.4; color:rgba(15,23,42,0.78);">
                            Manage courses, assign professors, enroll students.
                        </div>
                    </div>
                </div>

                <div style="margin-top:14px;">
                    <x-filament::button tag="a" href="{{ CourseResource::getUrl() }}" icon="heroicon-o-arrow-right" outlined>
                        Open Courses
                    </x-filament::button>
                </div>
            </div>

            {{-- USERS --}}
            <div data-card
                 style="flex:1 1 340px; border:1px solid rgba(0,0,0,0.12); border-radius:14px; padding:18px; background:#fff; box-shadow:0 1px 2px rgba(0,0,0,0.06); transition: box-shadow 150ms ease, transform 150ms ease;"
                 onmouseover="this.style.boxShadow = (window.__ql_isDark ? '0 10px 24px rgba(0,0,0,0.55)' : '0 10px 24px rgba(0,0,0,0.10)'); this.style.transform='translateY(-2px)';"
                 onmouseout="this.style.boxShadow = (window.__ql_isDark ? '0 1px 2px rgba(0,0,0,0.35)' : '0 1px 2px rgba(0,0,0,0.06)'); this.style.transform='translateY(0)';"
            >
                <div style="display:flex; gap:12px; align-items:flex-start;">
                    <div data-badge style="width:36px; height:36px; border-radius:10px; background:rgba(0,0,0,0.04); display:flex; align-items:center; justify-content:center;">
                        <x-filament::icon icon="heroicon-o-users" style="width:22px; height:22px;" />
                    </div>

                    <div style="flex:1;">
                        <div data-title style="font-size:18px; font-weight:700; line-height:1.2; margin:0; color:#0f172a;">
                            Users
                        </div>
                        <div data-desc style="margin-top:6px; font-size:13px; line-height:1.4; color:rgba(15,23,42,0.78);">
                            Manage users and roles.
                        </div>
                    </div>
                </div>

                <div style="margin-top:14px;">
                    <x-filament::button tag="a" href="{{ UserResource::getUrl() }}" icon="heroicon-o-arrow-right" outlined>
                        Open Users
                    </x-filament::button>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const root = document.documentElement;
                const host = document.getElementById('quick-links-root');
                if (!host) return;

                function readTheme() {
                    // Common Filament patterns:
                    // 1) <html data-theme="dark|light">
                    const dt = root.dataset.theme;
                    if (dt === 'dark' || dt === 'light') return dt;

                    // 2) localStorage theme flag
                    const ls = localStorage.getItem('theme') || localStorage.getItem('filament-theme');
                    if (ls === 'dark' || ls === 'light') return ls;

                    // 3) class-based
                    if (root.classList.contains('dark')) return 'dark';

                    return 'light';
                }

                function apply() {
                    const theme = readTheme();
                    window.__ql_isDark = (theme === 'dark');

                    host.querySelectorAll('[data-card]').forEach(card => {
                        card.style.background = window.__ql_isDark ? '#0b1220' : '#ffffff';
                        card.style.borderColor = window.__ql_isDark ? 'rgba(255,255,255,0.14)' : 'rgba(0,0,0,0.12)';
                        card.style.boxShadow = window.__ql_isDark ? '0 1px 2px rgba(0,0,0,0.35)' : '0 1px 2px rgba(0,0,0,0.06)';
                    });

                    host.querySelectorAll('[data-badge]').forEach(badge => {
                        badge.style.background = window.__ql_isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.04)';
                    });

                    host.querySelectorAll('[data-title]').forEach(el => {
                        el.style.color = window.__ql_isDark ? '#e5e7eb' : '#0f172a';
                    });

                    host.querySelectorAll('[data-desc]').forEach(el => {
                        el.style.color = window.__ql_isDark ? 'rgba(229,231,235,0.78)' : 'rgba(15,23,42,0.78)';
                    });
                }

                apply();

                // Watch for theme changes (data-theme/class toggles)
                new MutationObserver(apply).observe(root, { attributes: true });

                // Also react if Filament stores theme in localStorage (some toggles do this)
                window.addEventListener('storage', apply);
            })();
        </script>
    </x-filament::section>
</x-filament-widgets::widget>
