import { createReduxStore, register, subscribe, select } from "@wordpress/data";
import apiFetch from "@wordpress/api-fetch";
import { RECENTLY_USED_MAX } from "../constants.js";

const STORE_NAME = "sydney/pattern-library";

const INITIAL_FAVORITES = Array.isArray( window.sydneyPatternLibrary?.favorites )
  ? window.sydneyPatternLibrary.favorites
  : [];

const INITIAL_RECENTLY_USED = Array.isArray( window.sydneyPatternLibrary?.recentlyUsed )
  ? window.sydneyPatternLibrary.recentlyUsed
  : [];

const DEFAULT_STATE = {
  selectedType: "all",
  isModalOpen: false,
  searchQuery: "",
  favorites: INITIAL_FAVORITES,
  recentlyUsed: INITIAL_RECENTLY_USED,
};

const actions = {
  setType(patternType) {
    return { type: "SET_TYPE", patternType };
  },
  setSearch(query) {
    return { type: "SET_SEARCH", query };
  },
  openModal() {
    return { type: "OPEN_MODAL" };
  },
  closeModal() {
    return { type: "CLOSE_MODAL" };
  },
  toggleFavorite(patternName) {
    return { type: "TOGGLE_FAVORITE", patternName };
  },
  setFavorites(names) {
    return { type: "SET_FAVORITES", names };
  },
  trackPatternInsert(patternName) {
    return { type: "TRACK_PATTERN_INSERT", patternName };
  },
};

function reducer(state = DEFAULT_STATE, action) {
  switch (action.type) {
    case "SET_TYPE":
      return { ...state, selectedType: action.patternType };
    case "SET_SEARCH":
      return { ...state, searchQuery: action.query, selectedType: "all" };
    case "OPEN_MODAL":
      return { ...state, isModalOpen: true };
    case "CLOSE_MODAL":
      return { ...state, isModalOpen: false, searchQuery: "", selectedType: "all" };
    case "TOGGLE_FAVORITE": {
      const exists = state.favorites.includes(action.patternName);
      const favorites = exists
        ? state.favorites.filter((n) => n !== action.patternName)
        : [...state.favorites, action.patternName];
      // Auto-clear the favorites filter when the last favorite is removed,
      // so the user doesn't end up staring at an empty grid with no way out.
      const selectedType =
        favorites.length === 0 && state.selectedType === "favorites"
          ? "all"
          : state.selectedType;
      return { ...state, favorites, selectedType };
    }
    case "SET_FAVORITES":
      return { ...state, favorites: Array.isArray(action.names) ? action.names : [] };
    case "TRACK_PATTERN_INSERT": {
      const name = action.patternName;
      if ( typeof name !== "string" || ! name ) {
        return state;
      }
      // Move-to-front: drop any existing instance, prepend, truncate to cap.
      const next = [ name, ...state.recentlyUsed.filter( ( n ) => n !== name ) ].slice( 0, RECENTLY_USED_MAX );
      return { ...state, recentlyUsed: next };
    }
  }
  return state;
}

const selectors = {
  getSelectedType(state) {
    return state.selectedType;
  },
  getSearchQuery(state) {
    return state.searchQuery;
  },
  isModalOpen(state) {
    return state.isModalOpen;
  },
  getFavorites(state) {
    return state.favorites;
  },
  isFavorite(state, name) {
    return state.favorites.includes(name);
  },
  getRecentlyUsed(state) {
    return state.recentlyUsed;
  },
};

const store = createReduxStore(STORE_NAME, { reducer, actions, selectors });
register(store);

// Debounced persistence: coalesce rapid changes into a single POST per slice.
// Favorites and recently-used use independent timers so a failure on one
// doesn't stall the other.
const PERSIST_DEBOUNCE_MS = 400;

let lastPersistedFavorites = JSON.stringify(INITIAL_FAVORITES);
let favoritesPersistTimer = null;

subscribe(() => {
  const current = select(STORE_NAME).getFavorites();
  const serialized = JSON.stringify(current);
  if (serialized === lastPersistedFavorites) {
    return;
  }
  lastPersistedFavorites = serialized;

  if (favoritesPersistTimer) {
    clearTimeout(favoritesPersistTimer);
  }
  favoritesPersistTimer = setTimeout(() => {
    favoritesPersistTimer = null;
    apiFetch({
      path: "/sydney/v1/pattern-favorites",
      method: "POST",
      data: { favorites: current },
    }).catch((error) => {
      // Quiet failure — favorites are a personal preference, not load-bearing.
      // Next successful write will resync the array.
      // eslint-disable-next-line no-console
      console.error("Sydney pattern library: failed to persist favorites", error);
    });
  }, PERSIST_DEBOUNCE_MS);
});

let lastPersistedRecentlyUsed = JSON.stringify(INITIAL_RECENTLY_USED);
let recentlyUsedPersistTimer = null;

subscribe(() => {
  const current = select(STORE_NAME).getRecentlyUsed();
  const serialized = JSON.stringify(current);
  if (serialized === lastPersistedRecentlyUsed) {
    return;
  }
  lastPersistedRecentlyUsed = serialized;

  if (recentlyUsedPersistTimer) {
    clearTimeout(recentlyUsedPersistTimer);
  }
  recentlyUsedPersistTimer = setTimeout(() => {
    recentlyUsedPersistTimer = null;
    apiFetch({
      path: "/sydney/v1/pattern-recently-used",
      method: "POST",
      data: { recently_used: current },
    }).catch((error) => {
      // Quiet failure — recently-used is convenience surface, not load-bearing.
      // Next successful write will resync the array.
      // eslint-disable-next-line no-console
      console.error("Sydney pattern library: failed to persist recently used", error);
    });
  }, PERSIST_DEBOUNCE_MS);
});

export { STORE_NAME };
