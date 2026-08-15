<?php

// --- Arrow state ---

test('prev arrow is disabled at start', function () {
    $this->visit('/_ws/test/ui/slider')
        ->assertScript("document.querySelector('#slider-basic .ws-slider-arrow-prev').disabled === true");
});

test('next arrow is enabled when more slides exist', function () {
    $this->visit('/_ws/test/ui/slider')
        ->assertScript("document.querySelector('#slider-basic .ws-slider-arrow-next').disabled === false");
});

test('both arrows disabled when slides equal columns', function () {
    $this->visit('/_ws/test/ui/slider')
        ->assertScript("(() => {
            const prev = document.querySelector('#slider-exact .ws-slider-arrow-prev');
            const next = document.querySelector('#slider-exact .ws-slider-arrow-next');
            return prev.disabled === true && next.disabled === true;
        })()");
});

// --- Navigation: clicking arrows ---

test('clicking next moves slider forward', function () {
    $this->visit('/_ws/test/ui/slider')
        ->click('#slider-basic .ws-slider-arrow-next')
        ->assertScript(js_after_tick("(() => {
            const inner = document.querySelector('#slider-basic .ws-slider-inner');
            return inner.style.transform.includes('translateX') && !inner.style.transform.includes('translateX(0');
        })()"));
});

test('clicking next enables prev arrow', function () {
    $this->visit('/_ws/test/ui/slider')
        ->click('#slider-basic .ws-slider-arrow-next')
        ->assertScript(js_after_tick(
            "document.querySelector('#slider-basic .ws-slider-arrow-prev').disabled === false"
        ));
});

test('clicking next until end disables next arrow', function () {
    $this->visit('/_ws/test/ui/slider')
        ->assertScript("(() => {
            const next = document.querySelector('#slider-basic .ws-slider-arrow-next');
            next.click(); next.click(); next.click();
            return true;
        })()")
        ->assertScript(js_after_tick(
            "document.querySelector('#slider-basic .ws-slider-arrow-next').disabled === true"
        ));
});

test('clicking prev moves slider back', function () {
    $this->visit('/_ws/test/ui/slider')
        ->assertScript("(() => {
            const next = document.querySelector('#slider-basic .ws-slider-arrow-next');
            next.click(); next.click();
            return true;
        })()")
        ->assertScript(js_after_tick("(() => {
            document.querySelector('#slider-basic .ws-slider-arrow-prev').click();
            return true;
        })()"))
        ->assertScript(js_after_tick("(() => {
            const inner = document.querySelector('#slider-basic .ws-slider-inner');
            const tx = parseFloat(inner.style.transform.replace('translateX(', '').replace('px)', ''));
            return tx < 0;
        })()"));
});

// --- Navigation: scrollBy ---

test('scroll-by 2 advances by 2 slides per click', function () {
    $this->visit('/_ws/test/ui/slider')
        ->assertScript("(() => {
            const next = document.querySelector('#slider-scroll-by .ws-slider-arrow-next');
            next.click(); next.click();
            return true;
        })()")
        ->assertScript(js_after_tick(
            "document.querySelector('#slider-scroll-by .ws-slider-arrow-next').disabled === true"
        ));
});

// --- Navigation: scrollPage ---

test('scroll-page advances by visible columns per click', function () {
    $this->visit('/_ws/test/ui/slider')
        ->assertScript("(() => {
            const next = document.querySelector('#slider-scroll-page .ws-slider-arrow-next');
            next.click(); next.click();
            return true;
        })()")
        ->assertScript(js_after_tick(
            "document.querySelector('#slider-scroll-page .ws-slider-arrow-next').disabled === true"
        ));
});

// --- Navigation: scrollPage overrides scrollBy ---

test('scroll-page ignores scroll-by and advances by columns', function () {
    // slider-page-override: 2 cols, scroll-by=1, scroll-page=true, 6 slides
    // If scrollBy were used: 1 click = offset 1. If scrollPage used: 1 click = offset 2.
    $this->visit('/_ws/test/ui/slider')
        ->click('#slider-page-override .ws-slider-arrow-next')
        ->assertScript(js_after_tick("(() => {
            const inner = document.querySelector('#slider-page-override .ws-slider-inner');
            const slide = inner.querySelector('.ws-slider-slide');
            const gap = parseFloat(getComputedStyle(inner).columnGap) || 0;
            const expected = -(2 * (slide.offsetWidth + gap));
            const actual = parseFloat(inner.style.transform.replace('translateX(', '').replace('px)', ''));
            return Math.abs(actual - expected) < 1;
        })()"));
});

// --- Navigation: custom step ---

test('next with custom step advances by that amount', function () {
    // slider-events: 1 col, 3 slides. next(id, 2) should jump offset to 2.
    $this->visit('/_ws/test/ui/slider')
        ->assertScript(js_wirestrap("slider.next('slider-events', 2)"))
        ->assertScript(js_after_tick(
            "document.getElementById('event-offset').textContent === '2'"
        ));
});

test('prev with custom step goes back by that amount', function () {
    // Start at last (offset 2), then prev(id, 2) → offset 0.
    $this->visit('/_ws/test/ui/slider')
        ->assertScript(js_wirestrap("slider.last('slider-events')"))
        ->assertScript(js_after_tick('true'))
        ->assertScript(js_wirestrap("slider.prev('slider-events', 2)"))
        ->assertScript(js_after_tick(
            "document.getElementById('event-offset').textContent === '0'"
        ));
});

// --- Navigation: transform ---

test('transform is translateX(0px) at offset 0', function () {
    $this->visit('/_ws/test/ui/slider')
        ->assertScript("(() => {
            const t = document.querySelector('#slider-basic .ws-slider-inner').style.transform;
            return t === '' || t === 'translateX(0px)' || t === 'none';
        })()");
});

// --- External control ($wirestrap magic) ---

test('magic next updates offset', function () {
    $this->visit('/_ws/test/ui/slider')
        ->click('#btn-ev-next')
        ->assertScript(js_after_tick(
            "document.getElementById('event-offset').textContent === '1'"
        ));
});

test('magic prev navigates back', function () {
    $this->visit('/_ws/test/ui/slider')
        ->click('#btn-ev-next')
        ->assertScript(js_after_tick('true'))
        ->click('#btn-ev-prev')
        ->assertScript(js_after_tick(
            "document.getElementById('event-offset').textContent === '0'"
        ));
});

test('magic goTo jumps to specific offset', function () {
    $this->visit('/_ws/test/ui/slider')
        ->click('#btn-ev-goto1')
        ->assertScript(js_after_tick(
            "document.getElementById('event-offset').textContent === '1'"
        ));
});

test('magic first jumps to offset 0', function () {
    $this->visit('/_ws/test/ui/slider')
        ->click('#btn-ev-last')
        ->assertScript(js_after_tick('true'))
        ->click('#btn-ev-first')
        ->assertScript(js_after_tick(
            "document.getElementById('event-offset').textContent === '0'"
        ));
});

test('magic last jumps to max offset', function () {
    $this->visit('/_ws/test/ui/slider')
        ->click('#btn-ev-last')
        ->assertScript(js_after_tick(
            "document.getElementById('event-offset').textContent === '2'"
        ));
});

test('ws-slider-change reports canScrollPrev and canScrollNext', function () {
    $this->visit('/_ws/test/ui/slider')
        ->assertScript("document.getElementById('event-can-prev').textContent === 'false'")
        ->assertScript("document.getElementById('event-can-next').textContent === 'true'")
        ->click('#btn-ev-goto1')
        ->assertScript(js_after_tick(
            "document.getElementById('event-can-prev').textContent === 'true'"
        ))
        ->assertScript("document.getElementById('event-can-next').textContent === 'true'")
        ->click('#btn-ev-last')
        ->assertScript(js_after_tick(
            "document.getElementById('event-can-next').textContent === 'false'"
        ))
        ->assertScript("document.getElementById('event-can-prev').textContent === 'true'");
});

// --- Clamping ---

test('goTo clamps to max offset', function () {
    $this->visit('/_ws/test/ui/slider')
        ->assertScript("(() => {
            document.getElementById('slider-events').dispatchEvent(
                new CustomEvent('ws-slider', { detail: { action: 'goTo', index: 99 } })
            );
            return true;
        })()")
        ->assertScript(js_after_tick(
            "document.getElementById('event-offset').textContent === '2'"
        ));
});

test('goTo clamps to 0 for negative index', function () {
    $this->visit('/_ws/test/ui/slider')
        ->assertScript("(() => {
            document.getElementById('slider-events').dispatchEvent(
                new CustomEvent('ws-slider', { detail: { action: 'goTo', index: -5 } })
            );
            return true;
        })()")
        ->assertScript(js_after_tick(
            "document.getElementById('event-offset').textContent === '0'"
        ));
});
