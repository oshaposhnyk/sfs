// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Interactive Living Labs & partners map (About page).
 *
 * Self-contained: Leaflet is shipped with the theme and the country borders
 * come from a bundled Natural Earth GeoJSON — no external tile servers, so
 * no visitor data leaves the site. The static dot map in the template is the
 * no-JS fallback and is replaced when this module mounts.
 *
 * The GeoJSON is a static theme asset, not a web service, hence fetch()
 * rather than core/ajax (which only speaks to Moodle external functions).
 *
 * @module     theme_securefood/aboutmap
 * @copyright  2026 SecureFood School
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as L from 'theme_securefood/leaflet';
import {announce} from 'theme_securefood/live';

const token = (name, fallback) => {
    const value = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
    return value !== '' ? value : fallback;
};

const ACTIVE_ROW_CLASS = 'sfs-hubs__row--active';

/**
 * Clear every highlighted hub-list row.
 */
const clearHubHighlight = () => {
    document.querySelectorAll('.' + ACTIVE_ROW_CLASS).forEach((row) => {
        row.classList.remove(ACTIVE_ROW_CLASS);
    });
};

/**
 * Highlight a hub-list row that corresponds to a map marker.
 *
 * @param {String} hubid Stable DOM id exported by the About context.
 * @param {Boolean} scroll Whether the list should scroll the row into view.
 */
const highlightHub = (hubid, scroll = false) => {
    if (!hubid) {
        return;
    }
    const row = document.getElementById(hubid);
    if (!row) {
        return;
    }
    clearHubHighlight();
    row.classList.add(ACTIVE_ROW_CLASS);
    if (scroll) {
        row.scrollIntoView({block: 'nearest'});
    }
};

/**
 * Mount the map into the About hubs panel.
 *
 * @param {Object} config
 * @param {Array} config.hubs [{hubid, name, country, islab, markerlabel, lat, lon}]
 * @param {String} config.geourl URL of the bundled Europe GeoJSON.
 * @param {String} config.maplabel Accessible label for the map region.
 * @param {String} config.lablabel Popup label for Living Labs.
 * @param {String} config.partnerlabel Popup label for partners.
 */
export const init = async(config) => {
    const container = document.querySelector('.sfs-hubs__map');
    if (!container || container.dataset.sfsMapMounted) {
        return;
    }

    let geojson = null;
    try {
        const response = await fetch(config.geourl);
        geojson = await response.json();
    } catch (e) {
        return; // Keep the static fallback.
    }

    container.dataset.sfsMapMounted = '1';
    container.innerHTML = '';
    container.classList.add('sfs-hubs__map--live');
    container.removeAttribute('aria-hidden');
    container.setAttribute('role', 'region');
    container.setAttribute('aria-label', config.maplabel);

    const land = token('--sfs-primary50', '#E6F0F2');
    const border = token('--sfs-line', '#E8E1D4');
    const ink = token('--sfs-muted', '#5D7079');

    const map = L.map(container, {
        attributionControl: false,
        scrollWheelZoom: false,
        zoomSnap: 0.25,
        minZoom: 3,
        maxZoom: 8,
    });
    // A view must exist before any vector layer is added (the renderer has
    // no clip bounds until the first view is set).
    map.setView([50, 15], 4);

    L.geoJSON(geojson, {
        style: {
            color: border,
            weight: 1,
            fillColor: land,
            fillOpacity: 1,
        },
    }).addTo(map);

    const points = [];
    const safe = (text) => {
        const el = document.createElement('span');
        el.textContent = text || '';
        return el.innerHTML;
    };

    (config.hubs || []).forEach((hub) => {
        if (typeof hub.lat !== 'number' || typeof hub.lon !== 'number') {
            return;
        }
        const status = hub.islab ? config.lablabel : config.partnerlabel;
        const label = hub.markerlabel || [hub.name, hub.country, status].filter(Boolean).join(', ');
        const size = hub.islab ? 14 : 10;
        const marker = L.marker([hub.lat, hub.lon], {
            icon: L.divIcon({
                className: 'sfs-hubs__live-marker' + (hub.islab ? ' sfs-hubs__live-marker--lab' : ''),
                iconSize: [size, size],
                iconAnchor: [size / 2, size / 2],
                popupAnchor: [0, -(size / 2)],
            }),
            keyboard: true,
            title: label,
            alt: label,
        });
        marker.on('add', () => {
            const element = marker.getElement();
            if (!element) {
                return;
            }
            element.setAttribute('role', 'button');
            element.setAttribute('aria-label', label);
            if (hub.hubid) {
                element.setAttribute('aria-describedby', hub.hubid);
            }
            element.addEventListener('mouseenter', () => highlightHub(hub.hubid));
            element.addEventListener('mouseleave', clearHubHighlight);
            element.addEventListener('focus', () => {
                highlightHub(hub.hubid, true);
                announce(label);
            });
            element.addEventListener('blur', clearHubHighlight);
        });
        marker.on('click', () => {
            highlightHub(hub.hubid, true);
            announce(label);
        });
        marker.bindPopup(
            '<strong>' + safe(hub.name) + '</strong><br>' + safe(hub.country) + '<br><em>' + safe(status) + '</em>'
        );
        marker.addTo(map);
        const row = hub.hubid ? document.getElementById(hub.hubid) : null;
        if (row) {
            row.addEventListener('mouseenter', () => {
                highlightHub(hub.hubid);
                marker.openPopup();
            });
            row.addEventListener('mouseleave', () => {
                clearHubHighlight();
                marker.closePopup();
            });
        }
        points.push([hub.lat, hub.lon]);
    });

    if (points.length) {
        map.fitBounds(points, {padding: [24, 24]});
    }

    // Attribution without external links, kept in the corner for licencing.
    L.control.attribution({prefix: false})
        .addAttribution('Leaflet · Natural Earth')
        .addTo(map);

    map.getContainer().style.background = token('--sfs-surface2', '#F1EBE0');
    map.getContainer().style.setProperty('--sfs-map-ink', ink);
};
