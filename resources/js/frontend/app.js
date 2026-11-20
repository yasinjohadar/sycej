/**
 * SyCEJ public website behaviour: theme toggle, mobile drawer, header state,
 * hero title reveal, scroll reveals, card spotlight, hero parallax, areas
 * scroll-spy, reading progress, media filter and click-to-load YouTube embeds.
 * No dependencies.
 */

const THEME_KEY = 'sycej-theme';
const root = document.documentElement;
const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

function currentTheme() {
    return root.getAttribute('data-theme') || (prefersDark.matches ? 'dark' : 'light');
}

function initThemeToggle() {
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        const sync = () => button.setAttribute('aria-pressed', String(currentTheme() === 'dark'));
        sync();

        button.addEventListener('click', () => {
            const next = currentTheme() === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            try {
                localStorage.setItem(THEME_KEY, next);
            } catch {
                // Storage can be blocked; the choice still applies to this page.
            }
            sync();
        });

        prefersDark.addEventListener('change', sync);
    });
}

function initMenu() {
    const nav = document.querySelector('[data-nav]');
    const toggle = document.querySelector('[data-menu-toggle]');
    const scrim = document.querySelector('.fe-nav-scrim');

    if (!nav || !toggle) {
        return;
    }

    const setOpen = (open) => {
        nav.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', String(open));
        if (scrim) {
            scrim.hidden = !open;
        }
        document.body.style.overflow = open ? 'hidden' : '';
        if (open) {
            nav.querySelector('a, button')?.focus();
        }
    };

    toggle.addEventListener('click', () => setOpen(!nav.classList.contains('is-open')));

    document.querySelectorAll('[data-menu-close]').forEach((element) => {
        element.addEventListener('click', () => {
            setOpen(false);
            toggle.focus();
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && nav.classList.contains('is-open')) {
            setOpen(false);
            toggle.focus();
        }
    });
}

/**
 * Scrolled state for the header, the reading progress bar and the back-to-top
 * button, all driven from one passive scroll listener. The header's real height
 * is published as --fe-header-h so heroes can slide underneath it exactly.
 */
function initScrollState() {
    const header = document.querySelector('[data-header]');
    const progress = document.querySelector('[data-progress]');
    const toTop = document.querySelector('[data-totop]');

    if (header && 'ResizeObserver' in window) {
        new ResizeObserver(() => {
            root.style.setProperty('--fe-header-h', `${header.offsetHeight}px`);
        }).observe(header);
    }

    let ticking = false;

    const update = () => {
        const y = window.scrollY;
        const max = document.documentElement.scrollHeight - window.innerHeight;

        header?.toggleAttribute('data-scrolled', y > 24);
        progress?.style.setProperty('--progress', max > 0 ? String(Math.min(y / max, 1)) : '0');
        toTop?.classList.toggle('is-shown', y > window.innerHeight * 0.8);
        ticking = false;
    };

    update();
    window.addEventListener(
        'scroll',
        () => {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(update);
            }
        },
        { passive: true },
    );
}

/**
 * Wraps each word of a [data-split] title in a span so the words can rise in
 * turn. Splitting on whitespace only keeps Arabic letter joining intact; an
 * element child (the highlighted word) is treated as one word.
 */
function initSplitTitles() {
    if (reducedMotion) {
        return;
    }

    document.querySelectorAll('[data-split]').forEach((title) => {
        let index = 0;
        const fragment = document.createDocumentFragment();

        const wrap = (node) => {
            const span = document.createElement('span');
            span.className = 'fe-word';
            span.style.setProperty('--w', String(index++));
            span.appendChild(node);
            return span;
        };

        Array.from(title.childNodes).forEach((node) => {
            if (node.nodeType !== Node.TEXT_NODE) {
                fragment.appendChild(wrap(node));
                return;
            }

            node.textContent.split(/(\s+)/).forEach((part) => {
                if (part === '') {
                    return;
                }

                fragment.appendChild(/^\s+$/.test(part) ? document.createTextNode(part) : wrap(document.createTextNode(part)));
            });
        });

        title.replaceChildren(fragment);
    });
}

function initReveal() {
    const items = document.querySelectorAll('[data-reveal]');

    if (reducedMotion || !('IntersectionObserver' in window)) {
        items.forEach((item) => item.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { rootMargin: '0px 0px -8% 0px', threshold: 0.12 },
    );

    items.forEach((item) => observer.observe(item));
}

/** Feeds the pointer position to [data-spotlight] cards as --mx / --my. */
function initSpotlight() {
    if (!finePointer) {
        return;
    }

    document.addEventListener(
        'pointermove',
        (event) => {
            const card = event.target.closest?.('[data-spotlight]');

            if (!card) {
                return;
            }

            const rect = card.getBoundingClientRect();
            card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            card.style.setProperty('--my', `${event.clientY - rect.top}px`);
        },
        { passive: true },
    );
}

/** Layers of the home hero scene drift against the pointer at their own depth. */
function initParallax() {
    const scene = document.querySelector('[data-parallax]');

    if (!scene || reducedMotion || !finePointer) {
        return;
    }

    const hero = scene.closest('[data-hero]') ?? scene;
    const layers = Array.from(scene.querySelectorAll('[data-depth]'));
    let frame = 0;

    hero.addEventListener('pointermove', (event) => {
        cancelAnimationFrame(frame);
        frame = requestAnimationFrame(() => {
            const rect = hero.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - 0.5;
            const y = (event.clientY - rect.top) / rect.height - 0.5;

            layers.forEach((layer) => {
                const depth = Number(layer.dataset.depth) || 0;
                layer.style.transform = `translate3d(${(-x * depth).toFixed(1)}px, ${(-y * depth).toFixed(1)}px, 0)`;
            });
        });
    });

    hero.addEventListener('pointerleave', () => {
        layers.forEach((layer) => {
            layer.style.transform = '';
        });
    });
}

/**
 * "Who we work for": hovering or focusing an audience lights its node and line
 * in the hub, and vice versa. Untouched, the highlight walks through the list.
 */
function initAudience() {
    const section = document.querySelector('[data-audience]');

    if (!section) {
        return;
    }

    const parts = Array.from(section.querySelectorAll('[data-audience-key]'));
    const count = section.querySelectorAll('.fe-audience__item').length;
    let current = 0;
    let timer = 0;

    const activate = (key) => {
        current = Number(key);
        parts.forEach((part) => part.classList.toggle('is-active', Number(part.dataset.audienceKey) === current));
    };

    const stop = () => window.clearInterval(timer);
    const start = () => {
        stop();
        if (!reducedMotion) {
            timer = window.setInterval(() => activate((current + 1) % count), 3200);
        }
    };

    activate(0);

    parts.forEach((part) => {
        const select = () => {
            stop();
            activate(part.dataset.audienceKey);
        };
        part.addEventListener('pointerenter', select);
        part.addEventListener('focus', select);
    });

    section.addEventListener('pointerleave', start);

    if ('IntersectionObserver' in window) {
        new IntersectionObserver((entries) => {
            entries.forEach((entry) => (entry.isIntersecting ? start() : stop()));
        }).observe(section);
    }
}

/**
 * Article page: builds the table of contents from the article's h2/h3, marks
 * the heading in view, and copies the article link to the clipboard.
 */
function initArticle() {
    const content = document.querySelector('[data-article-content]');
    const toc = document.querySelector('[data-toc]');
    const list = document.querySelector('[data-toc-list]');

    if (content && toc && list) {
        const headings = Array.from(content.querySelectorAll('h2, h3'));

        if (headings.length >= 2) {
            const links = headings.map((heading, index) => {
                heading.id ||= `section-${index + 1}`;

                const item = document.createElement('li');
                const link = document.createElement('a');
                link.href = `#${heading.id}`;
                link.textContent = heading.textContent.trim();
                link.classList.toggle('is-sub', heading.tagName === 'H3');
                item.appendChild(link);
                list.appendChild(item);

                return link;
            });

            toc.hidden = false;

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver(
                    (entries) => {
                        entries.forEach((entry) => {
                            if (!entry.isIntersecting) {
                                return;
                            }

                            links.forEach((link) => {
                                if (link.hash.slice(1) === entry.target.id) {
                                    link.setAttribute('aria-current', 'true');
                                } else {
                                    link.removeAttribute('aria-current');
                                }
                            });
                        });
                    },
                    { rootMargin: '-20% 0px -70% 0px' },
                );

                headings.forEach((heading) => observer.observe(heading));
            }
        }
    }

    // Reading progress ring: how far through the article body the reader is.
    const ring = document.querySelector('[data-read-ring]');
    const percent = document.querySelector('[data-read-percent]');

    if (content && ring) {
        const card = ring.closest('.fe-readcard');
        let ticking = false;

        const update = () => {
            const rect = content.getBoundingClientRect();
            const travelled = window.innerHeight * 0.35 - rect.top;
            const progress = Math.min(Math.max(travelled / rect.height, 0), 1);

            card?.style.setProperty('--read', progress.toFixed(3));
            if (percent) {
                percent.textContent = `${Math.round(progress * 100)}%`;
            }
            ticking = false;
        };

        update();
        window.addEventListener(
            'scroll',
            () => {
                if (!ticking) {
                    ticking = true;
                    requestAnimationFrame(update);
                }
            },
            { passive: true },
        );
    }

    document.querySelectorAll('[data-copy-link]').forEach((button) => {
        const toast = button.closest('.fe-share')?.querySelector('[data-copy-toast]');

        button.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(button.dataset.copyLink);
                if (toast) {
                    toast.textContent = button.dataset.copied;
                    window.setTimeout(() => {
                        toast.textContent = '';
                    }, 2400);
                }
            } catch {
                window.prompt('', button.dataset.copyLink);
            }
        });
    });
}

/** Marks the areas-page index entry whose section is currently in view. */
function initScrollSpy() {
    const spy = document.querySelector('[data-spy]');

    if (!spy || !('IntersectionObserver' in window)) {
        return;
    }

    const links = new Map(Array.from(spy.querySelectorAll('a[href^="#"]')).map((link) => [link.hash.slice(1), link]));

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                links.forEach((link, id) => {
                    if (id === entry.target.id) {
                        link.setAttribute('aria-current', 'true');
                    } else {
                        link.removeAttribute('aria-current');
                    }
                });
            });
        },
        { rootMargin: '-35% 0px -55% 0px' },
    );

    document.querySelectorAll('[data-spy-target]').forEach((section) => observer.observe(section));
}

function initMediaFilter() {
    const group = document.querySelector('[data-media-filter]');
    const grid = document.querySelector('[data-media-grid]');

    if (!group || !grid) {
        return;
    }

    group.addEventListener('click', (event) => {
        const button = event.target.closest('[data-filter]');

        if (!button) {
            return;
        }

        const kind = button.dataset.filter;

        group.querySelectorAll('[data-filter]').forEach((other) => {
            other.setAttribute('aria-pressed', String(other === button));
        });

        grid.querySelectorAll('[data-kind]').forEach((card) => {
            card.hidden = kind !== 'all' && card.dataset.kind !== kind;
        });
    });
}

function initVideos() {
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-video]');

        if (!button) {
            return;
        }

        const id = button.dataset.video;

        if (!/^[\w-]{11}$/.test(id)) {
            return;
        }

        const frame = document.createElement('iframe');
        frame.src = `https://www.youtube-nocookie.com/embed/${id}?autoplay=1&rel=0`;
        frame.title = button.dataset.videoTitle || 'YouTube';
        frame.allow = 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture';
        frame.allowFullscreen = true;
        frame.referrerPolicy = 'strict-origin-when-cross-origin';

        button.parentElement.replaceChildren(frame);
    });
}

initThemeToggle();
initMenu();
initScrollState();
initSplitTitles();
initReveal();
initSpotlight();
initParallax();
initScrollSpy();
initAudience();
initArticle();
initMediaFilter();
initVideos();
