/**
 * Air theme JavaScript, vanilla and buildless.
 *
 * Replaces the old webpack bundle (jQuery, Vue, moment, frappe-charts,
 * fitvids, what-input, lazyload). Everything here runs on plain platform
 * APIs so there is no build step and nothing to keep updated.
 *
 * Loaded in the footer, so the DOM is parsed by the time this runs.
 * Conditional extras live in their own files: heatmap.js (diary archive)
 * and prism.js (singulars with code blocks).
 */

(() => {
  'use strict';

  // JavaScript is active: swap the body class
  document.body.classList.remove('no-js');
  document.body.classList.add('js');

  /**
   * what-input replacement: expose the current input method as
   * [data-whatinput] on <html> for the focus styles in base/_accessibility.
   */
  const setInput = (method) => document.documentElement.setAttribute('data-whatinput', method);
  setInput('mouse');
  document.addEventListener('pointerdown', (e) => setInput('touch' === e.pointerType ? 'touch' : 'mouse'), { passive: true });
  document.addEventListener('keydown', (e) => {
    if ([ 'Tab', 'ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'Enter', ' ', 'Escape' ].includes(e.key)) {
      setInput('keyboard');
    }
  }, { passive: true });

  // Localized strings helper
  const t = (key) => {
    const strings = window.minimalistmadness_screenReaderText || {};
    return typeof strings[key] === 'undefined' ? '' : strings[key];
  };

  const debounce = (fn, wait) => {
    let timeout;
    return (...args) => {
      clearTimeout(timeout);
      timeout = setTimeout(() => fn(...args), wait);
    };
  };

  /**
   * External link labels and indicator arrows
   */
  const isLinkExternal = (href, localDomains) => {
    if (!href.length) return false;
    if (/^(#|tel:|mailto:|\/)/.test(href)) return false;
    let url;
    try {
      url = new URL(href);
    } catch (error) {
      return false;
    }
    return !localDomains.some((domain) => url.host === domain);
  };

  const getChildAltText = (link) => {
    const children = [...link.children];
    if (!children.length) return '';
    const imgs = children.filter((child) => child.tagName.toLowerCase() === 'img');
    if (children.length !== imgs.length) return '';
    return imgs.filter((img) => img.alt && img.alt !== '').map((img) => img.alt).join(', ');
  };

  const styleExternalLinks = () => {
    const localDomains = [ window.location.host ].concat(window.minimalistmadness_externalLinkDomains || []);

    [...document.querySelectorAll('a')].filter((link) => isLinkExternal(link.href, localDomains)).forEach((link) => {
      if (link.childElementCount === 1 && link.children[0].tagName.toLowerCase() === 'img') return;

      if (!link.classList.contains('no-external-link-label')) {
        const text = link.textContent.trim().length ? link.textContent.trim() : getChildAltText(link);
        link.setAttribute('aria-label', link.target === '_blank'
          ? `${text}: ${t('external_link')}, ${t('target_blank')}`
          : `${text}: ${t('external_link')}`);
      }

      if (![ 'no-external-link-indicator', 'global-link', 'button' ].some((cls) => link.classList.contains(cls))) {
        link.insertAdjacentHTML('beforeend', '<svg class="external-link-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9"><path d="M4.499 1.497h4v4m0-4l-7 7" fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path></svg>');
      }
    });

    // Aria-labels for image-only links
    [...document.querySelectorAll('a')].forEach((link) => {
      if (link.textContent.trim() !== '' || link.ariaLabel) return;
      const label = getChildAltText(link);
      if (label !== '') link.ariaLabel = label;
    });
  };

  styleExternalLinks();

  // Mastodon embed script, only when the page actually has an embed
  if (document.querySelector('iframe[src*="mementomori.social"]')) {
    const tag = document.createElement('script');
    tag.src = 'https://mementomori.social/embed.js';
    document.head.appendChild(tag);
  }

  /**
   * Most read posts counter, one count per post per hour per tab
   */
  if (typeof window.mostRead !== 'undefined') {
    const key = `mostread_${window.mostRead.id}`;
    const now = Date.now();
    if (now - Number(sessionStorage.getItem(key)) > 3600000) {
      sessionStorage.setItem(key, now);
      fetch(window.mostRead.url, { method: 'POST', keepalive: true });
    }
  }

  /**
   * Own ad, with permanent dismiss
   */
  const adContent = '<p class="promotion-info">Sori häiriö, tämä on härski oman firmani mainos, teksti jatkuu alapuolella...</p><a href="https://www.dude.fi/yhteystiedot" class="global-link" aria-hidden="true" tabindex="-1"></a><div class="spans"><div class="span span-first"><div class="inner"><h2 class="screen-reader-text">Digitoimisto Dude Oy -mainos:</h2><svg aria-hidden="true" width="110" height="21.98" xmlns="http://www.w3.org/2000/svg" x="0" y="0" viewBox="0 0 2267.72 453.54" xml:space="preserve"><path fill="currentColor" d="M950.26 211.64c0 37.66-12.7 111.12-97.79 111.12-85.61 0-98.4-73.47-98.4-111.12V5.23H590.91v217.92c0 138.73 97.78 221.55 261.55 221.55 163.39 0 260.93-82.82 260.93-221.55V5.23H950.26v206.41zM2264.41 127.17V5.23h-505.2v439.48h505.2V322.76h-345.08v-48.71h286.91v-98.17h-286.91v-48.71zM317.21 5.23H3v439.48h314.21c108.81 0 219.87-87.76 219.87-219.74 0-132.83-111.06-219.74-219.87-219.74zm-39.84 317.53H166.14v-195.4h111.23c57.58 0 97.7 45.79 97.7 97.61 0 52.51-40.12 97.79-97.7 97.79zM1485.51 5.23H1171.3v439.48h314.21c108.81 0 219.87-87.76 219.87-219.74 0-132.83-111.06-219.74-219.87-219.74zm-39.84 317.53h-111.23v-195.4h111.23c57.58 0 97.7 45.79 97.7 97.61 0 52.51-40.12 97.79-97.7 97.79z"/></svg></div></div><div class="span span-second"><h3 class="title">Tarvitsetko laadukkaat ja helposti päivitettävät verkkosivut?</h3><p>Nämäkin sivut joita juuri nyt katselet ovat käsintehtyä, kotimaista laatua. Toteuttamamme WordPress-verkkosivut latautuvat supernopeasti ja ovat naurettavan hyvännäköisiä. Emme käytä valmispalikoita, vaan suunnittelemme ja koodaamme kaikki käsin itse. Yrityksemme on ollut toiminnassa vuodesta 2013 ja kasvu on ollut tasaista. Meihin luottaa jo sadat asiakkaat. <a href="https://www.dude.fi/yhteystiedot">Tutustu lisää ja ota yhteyttä!</a> <button class="hide-forever-ad hide-forever"><svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" width="13" height="13"><path d="M11.559 1.042L1.042 11.912m0-10.87l10.517 10.87" stroke="currentColor" stroke-width="2" fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round"/></svg>Ei kiinnosta, piilota mainos pysyvästi</button></p></div>';

  if (window.localStorage.getItem('hide_dude_ad') !== '1') {
    const spawnAd = (referenceElement) => {
      const ownAd = document.createElement('div');
      ownAd.innerHTML = adContent;
      ownAd.classList.add('ownad-unblockable');
      referenceElement.parentNode.insertBefore(ownAd, referenceElement.nextSibling);
    };

    if (document.getElementById('article-text-content')) {
      const articleElement = document.querySelector('.container-article').children[0];
      if (articleElement && articleElement.children.length) {
        spawnAd(articleElement.children[Math.floor(articleElement.children.length / 3)]);
      }
    }

    const slot = document.getElementById('spawn-slot');
    if (slot) spawnAd(slot);
  }

  document.addEventListener('click', (e) => {
    if (e.target.closest && e.target.closest('.hide-forever-ad')) {
      const ownAd = document.querySelector('.ownad-unblockable');
      ownAd.classList.add('closing');
      window.setTimeout(() => ownAd.classList.add('dismissed'), 400);
      window.localStorage.setItem('hide_dude_ad', '1');
    }
  });

  /**
   * Random posts page (template-parts/random.php)
   */
  const dynamicContent = document.querySelector('.dynamic-content');
  if (dynamicContent) {
    const loadRandom = () => {
      fetch('/random-dynamic/')
        .then((response) => response.text())
        .then((html) => { dynamicContent.innerHTML = html; });
    };
    loadRandom();
    document.querySelectorAll('.load-more-random').forEach((el) => el.addEventListener('click', (e) => {
      e.preventDefault();
      loadRandom();
    }));
  }

  /**
   * Search overlay
   */
  const searchOverlay = document.querySelector('.overlay-search');
  const searchInput = document.querySelector('.overlay-search .search-input');
  const searchResults = document.querySelector('ul.search-results');

  const closeSearch = () => {
    if (searchOverlay) searchOverlay.classList.remove('overlay-open');
    document.body.classList.remove('overlay-open', 'search-open');
    if (searchResults) searchResults.innerHTML = '';
    document.querySelectorAll('.search-mobile input, .overlay-search input').forEach((input) => { input.value = ''; });
  };

  closeSearch();

  document.querySelectorAll('.search-trigger').forEach((el) => el.addEventListener('click', (e) => {
    e.preventDefault();
    document.body.classList.remove('main-navigation-open', 'is-scrolling-prevented');
    if (searchOverlay) searchOverlay.classList.add('overlay-open');
    document.body.classList.add('overlay-open', 'search-open');
    if (searchInput) searchInput.focus();
  }));

  document.addEventListener('click', (e) => {
    if (e.target.closest && (e.target.closest('.button-close') || e.target.closest('.article--link'))) closeSearch();
  });

  document.addEventListener('keyup', (e) => {
    if (e.key === 'Escape' && document.body.classList.contains('search-open')) closeSearch();
  });

  if (searchInput && window.algolia) {
    const { appId, searchKey, index } = window.algolia;
    const endpoint = `https://${appId}-dsn.algolia.net/1/indexes/${index}/query`;
    const cache = new Map();

    const renderHits = (hits) => {
      if (!hits.length) {
        searchResults.innerHTML = '<li class="no-results"><h2>Ei hakutuloksia.</h2></li>';
        return;
      }
      searchResults.innerHTML = hits.map((hit) => `<li><a href="${hit.url}" class="global-link" aria-hidden="true" tabindex="-1"></a><span class="search-post-type">${hit.type_label} &middot; <span class="search-post-date">${hit.date_readable}</span></span><h2><a class="article--link" href="${hit.url}">${hit._highlightResult.title.value}</a></h2><div class="search-excerpt">${hit._snippetResult ? hit._snippetResult.content.value : ''}</div></li>`).join('');
    };

    // Free plan: 10K requests a month, so wait for 3 characters and a typing pause, and never ask twice
    const doSearch = debounce(() => {
      const search = searchInput.value.trim();

      if (search.length < 3) {
        searchResults.innerHTML = '';
        return;
      }

      if (cache.has(search)) {
        renderHits(cache.get(search));
        return;
      }

      fetch(endpoint, {
        method: 'POST',
        headers: { 'X-Algolia-Application-Id': appId, 'X-Algolia-API-Key': searchKey },
        body: JSON.stringify({ query: search }),
      })
        .then((response) => response.json())
        .then((result) => {
          cache.set(search, result.hits || []);
          if (searchInput.value.trim() === search) renderHits(result.hits || []);
        });
    }, 300);

    searchInput.addEventListener('input', doSearch);
  }

  /**
   * Comments: reveal the other fields when typing a comment
   */
  const commentField = document.querySelector('textarea#comment');
  if (commentField) {
    commentField.addEventListener('keyup', () => {
      document.querySelectorAll('.hidden-by-default').forEach((el) => el.classList.add('show'));
    }, { once: true });
  }

  /**
   * Front page load more (replaces the Vue construct)
   */
  const loadMoreButton = document.querySelector('.block-loadable button.load-more');
  if (loadMoreButton && typeof window.paged_query !== 'undefined') {
    const query = window.paged_query.posts_query;
    const feed = document.querySelector('.block-loadable .items-vue');
    const spinner = document.querySelector('.block-loadable .load-more-spinner');
    const buttonContainer = loadMoreButton.closest('.load-more-container');

    const renderPost = (post) => `<article class="entry post-card post no-animation item-vue" id="post-${post.id}">
      <div class="post-card-content">
        <a href="${post.link}" class="global-link" aria-label="${(post.title.rendered || '').replace(/"/g, '&quot;')}" aria-hidden="true" tabindex="-1"></a>
        <div class="post-card-image no-bottom-radius"><div class="img"><p class="post-card-details">${post.time_custom || ''}<br><span class="time-to-read">${post.reading_time_custom || ''} lukukokemus</span></p><div class="image image-background image-background-layer"><img src="${post.featured_image_custom || ''}" alt="" loading="lazy"></div></div></div>
        <div class="post-card-information">
          <h2 class="post-card-title-large"><a href="${post.link}">${post.title.rendered}</a></h2>
          <p>${post.excerpt || ''}</p>
        </div>
      </div>
    </article>`;

    loadMoreButton.addEventListener('click', (e) => {
      e.preventDefault();
      spinner.style.display = '';
      query.paged += 1;

      const params = new URLSearchParams({
        page: query.paged,
        per_page: query.posts_per_page,
        _fields: 'id,link,title,excerpt,time_custom,reading_time_custom,featured_image_custom',
      });
      (query.post__not_in || []).forEach((id) => params.append('exclude[]', id));

      fetch(`${window.air.baseurl}wp/v2/posts?${params}`)
        .then((response) => response.json())
        .then((posts) => {
          spinner.style.display = 'none';
          if (!posts || !posts.length) {
            buttonContainer.style.display = 'none';
            return;
          }
          feed.insertAdjacentHTML('beforeend', posts.map(renderPost).join(''));
          styleExternalLinks();
          buttonContainer.style.display = posts.length < window.air.posts_per_page ? 'none' : '';
        })
        .catch(() => { spinner.style.display = 'none'; });
    });
  }

  /**
   * Scroll indicator on single hero
   */
  const scrollIndicator = document.querySelector('.scroll-indicator');
  if (scrollIndicator) {
    window.addEventListener('scroll', () => {
      if (window.scrollY >= 200) {
        scrollIndicator.classList.add('fadeout');
        setTimeout(() => { scrollIndicator.style.display = 'none'; }, 500);
      } else {
        scrollIndicator.classList.remove('fadeout');
        setTimeout(() => { scrollIndicator.style.display = ''; }, 500);
      }
    }, { passive: true });
  }

  // Add size class to legacy-era images without one
  window.addEventListener('load', () => {
    document.querySelectorAll('.container-article img').forEach((img) => {
      if (img.clientWidth > 350) img.classList.add('size-large');
    });
  });

  /**
   * Mobile navigation
   */
  document.querySelectorAll('.nav-burger').forEach((el) => el.addEventListener('click', () => {
    document.body.classList.toggle('site-head-open');
  }));

  document.querySelectorAll('.menu-item a').forEach((el) => el.addEventListener('click', () => {
    document.body.classList.remove('site-head-open');
  }));

  /**
   * Rain
   */
  const makeItRain = () => {
    document.querySelectorAll('.rain').forEach((el) => { el.innerHTML = ''; });

    let increment = 0;
    let drops = '';
    let backDrops = '';

    while (increment < 100) {
      const randoHundo = Math.floor(Math.random() * 98 + 1);
      const randoFiver = Math.floor(Math.random() * 4 + 2);
      increment += randoFiver;
      const dropStyle = `bottom: ${randoFiver + randoFiver - 1 + 100}%; animation-delay: 0.${randoHundo}s; animation-duration: 0.5${randoHundo}s;`;
      const innerStyle = `animation-delay: 0.${randoHundo}s; animation-duration: 0.5${randoHundo}s;`;
      drops += `<div class="drop" style="left: ${increment}%; ${dropStyle}"><div class="stem" style="${innerStyle}"></div><div class="splat" style="${innerStyle}"></div></div>`;
      backDrops += `<div class="drop" style="right: ${increment}%; ${dropStyle}"><div class="stem" style="${innerStyle}"></div><div class="splat" style="${innerStyle}"></div></div>`;
    }

    const frontRow = document.querySelector('.rain.front-row');
    const backRow = document.querySelector('.rain.back-row');
    if (frontRow) frontRow.insertAdjacentHTML('beforeend', drops);
    if (backRow) backRow.insertAdjacentHTML('beforeend', backDrops);
  };

  const siteLogo = document.querySelector('.site-head-logo');
  if (siteLogo) {
    siteLogo.addEventListener('mouseenter', () => document.body.classList.add('splat-toggle'));
    siteLogo.addEventListener('mouseleave', () => document.body.classList.remove('splat-toggle'));
  }

  makeItRain();
})();
