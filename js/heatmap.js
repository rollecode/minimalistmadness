/**
 * Writing heatmap for the diary archive: a GitHub-style contribution
 * calendar of daily word counts for the past year. Vanilla replacement for
 * frappe-charts + moment (the old bundle shipped ~160 KB for this grid).
 *
 * Data comes from the `heatmapdata` global (localized on the diary archive
 * only): { <unix timestamp of local midnight>: <word count> }.
 */

(() => {
  'use strict';

  const el = document.getElementById('heatmap');
  if (!el || typeof window.heatmapdata === 'undefined') return;

  const localIso = (date) => `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;

  // Bucket word counts per local calendar day
  const counts = {};
  Object.entries(window.heatmapdata).forEach(([ ts, words ]) => {
    const key = localIso(new Date(ts * 1000));
    counts[key] = (counts[key] || 0) + Number(words);
  });

  const max = Math.max(...Object.values(counts), 1);
  const level = (value) => (value ? Math.min(4, Math.ceil(value / (max / 4))) : 0);

  // One year back from today, snapped to the previous Sunday
  const end = new Date();
  end.setHours(0, 0, 0, 0);
  const start = new Date(end);
  start.setFullYear(start.getFullYear() - 1);
  start.setDate(start.getDate() - start.getDay());

  const MONTHS = [ 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC' ];
  let cells = '';
  let monthSpans = '';
  let column = 0;
  let labeledMonth = -1;

  for (let day = new Date(start); day <= end; day.setDate(day.getDate() + 1)) {
    if (day.getDay() === 0) {
      column += 1;
      // Label each month at its first Sunday; the partial first week is
      // skipped so the leading label does not collide with the next one.
      if (day.getMonth() !== labeledMonth && day.getDate() <= 7) {
        labeledMonth = day.getMonth();
        monthSpans += `<span style="grid-column: ${column}">${MONTHS[labeledMonth]}</span>`;
      }
    }
    const value = counts[localIso(day)] || 0;
    const title = value ? ` title="${value} sanaa ${day.getDate()}.${day.getMonth() + 1}.${day.getFullYear()}"` : '';
    cells += `<div class="hm hm-l${level(value)}"${title}></div>`;
  }

  el.innerHTML = `<div class="chart-container heatmap-chart">
    <div class="heatmap-inner">
      <div class="heatmap-days" aria-hidden="true"><span></span><span>Mon</span><span></span><span>Wed</span><span></span><span>Fri</span><span></span></div>
      <div class="heatmap-weeks">
        <div class="heatmap-months" aria-hidden="true" style="grid-template-columns: repeat(${column}, var(--hm-cell))">${monthSpans}</div>
        <div class="heatmap-cells">${cells}</div>
      </div>
    </div>
    <div class="heatmap-legend" aria-hidden="true"><span class="heatmap-legend-label">Less</span><span class="hm hm-l0"></span><span class="hm hm-l1"></span><span class="hm hm-l2"></span><span class="hm hm-l3"></span><span class="hm hm-l4"></span><span class="heatmap-legend-label">More</span></div>
  </div>`;
})();
