import {
  Modal,
  Button,
  SearchControl,
  __experimentalHStack as HStack,
  __experimentalVStack as VStack,
} from "@wordpress/components";
import { close } from "@wordpress/icons";
import { useSelect, useDispatch } from "@wordpress/data";
import { __, sprintf } from "@wordpress/i18n";
import { STORE_NAME } from "../store/index.js";
import { TypeFilter } from "./TypeFilter.js";
import { PatternGrid } from "./PatternGrid.js";

export function PatternLibraryModal({ onClose, onInsert }) {
  // isResolving becomes false once the core pattern registry has finished loading.
  const { patterns, isLoading } = useSelect((select) => {
    const resolving = select("core").isResolving("getBlockPatterns");
    return {
      patterns: select("core").getBlockPatterns() || [],
      isLoading: resolving,
    };
  });

  const searchQuery = useSelect((select) => select(STORE_NAME).getSearchQuery());
  const selectedType = useSelect((select) => select(STORE_NAME).getSelectedType());
  const favorites = useSelect((select) => select(STORE_NAME).getFavorites());
  const recentlyUsed = useSelect((select) => select(STORE_NAME).getRecentlyUsed());
  const { setSearch, setType, trackPatternInsert } = useDispatch(STORE_NAME);

  const sydneyPatterns = patterns.filter((p) => p.name?.startsWith("sydney/"));

  const isFavoritesActive = selectedType === "favorites";
  const showFavoritesFilter = favorites.length > 0;
  const isRecentActive = selectedType === "recent";
  const showRecentFilter = recentlyUsed.length > 0;

  function handleInsert(pattern, blocks) {
    trackPatternInsert(pattern.name);
    onInsert(pattern, blocks);
  }

  return (
    <Modal
      onRequestClose={onClose}
      className="sydney-pattern-library-modal"
      __experimentalHideHeader={true}
      size="fill"
    >
      <HStack
        spacing={0}
        alignment="flex-start"
        expanded
        className="sydney-pattern-library-modal-container"
      >
        <VStack className="sydney-pattern-library-modal-sidebar" alignment="top">
          <div className="sydney-pattern-library-modal-title-container">
            <h2 className="sydney-pattern-library-modal-title">
              <svg
                width="35"
                height="36"
                viewBox="0 0 35 36"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M17.7169 0H34.3047V18.119L17.7169 0ZM0.161307 10.6334H0C0 7.00073 1.00369 4.32034 3.01107 2.59221C5.01846 0.86407 8.11017 0 12.2863 0L34.3047 26.2925C34.3047 29.7841 33.3459 32.2793 31.428 33.7782C29.564 35.2417 26.5171 35.9735 22.2873 35.9735L0.161307 10.6334ZM0.0806536 17.7752L16.0501 36H0.0806536V17.7752Z"
                  fill="#3c3c3c"
                />
              </svg>
              {__("Pattern Library", "sydney")}
            </h2>
          </div>
          <div className="sydney-pattern-library-modal-sidebar-content">
            <div className="sydney-pattern-library-modal-search">
              <SearchControl
                value={searchQuery}
                onChange={setSearch}
                placeholder={__("Search patterns", "sydney")}
                __nextHasNoMarginBottom
              />
            </div>
            <TypeFilter patterns={sydneyPatterns} />
          </div>
        </VStack>
        <div className="sydney-pattern-library-modal-content">
          <HStack
            spacing={4}
            alignment="center"
            justify="space-between"
            className="sydney-pattern-library-modal-header"
          >
            <div className="sydney-pattern-library-modal-header-left">
              {showRecentFilter && (
                <button
                  type="button"
                  className={`sydney-pattern-library-recently-used-filter${
                    isRecentActive ? " is-active" : ""
                  }`}
                  aria-pressed={isRecentActive}
                  onClick={() => setType(isRecentActive ? "all" : "recent")}
                >
                  <svg
                    width="14"
                    height="14"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    aria-hidden="true"
                    focusable="false"
                  >
                    <polyline points="1 4 1 10 7 10" />
                    <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10" />
                  </svg>
                  {__("Recently used", "sydney")}
                </button>
              )}
              {showFavoritesFilter && (
                <button
                  type="button"
                  className={`sydney-pattern-library-favorites-filter${
                    isFavoritesActive ? " is-active" : ""
                  }`}
                  aria-pressed={isFavoritesActive}
                  onClick={() => setType(isFavoritesActive ? "all" : "favorites")}
                >
                  <svg
                    width="14"
                    height="14"
                    viewBox="0 0 24 24"
                    fill={isFavoritesActive ? "currentColor" : "none"}
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    aria-hidden="true"
                    focusable="false"
                  >
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                  </svg>
                  {sprintf(
                    /* translators: %d: number of favorited patterns */
                    __("Favorites (%d)", "sydney"),
                    favorites.length
                  )}
                </button>
              )}
            </div>
            <Button
              icon={close}
              onClick={onClose}
              label={__("Close", "sydney")}
            />
          </HStack>
          <div className="sydney-pattern-library-modal-grid-container">
            <PatternGrid
              patterns={sydneyPatterns}
              isLoading={isLoading}
              onInsert={handleInsert}
            />
          </div>
        </div>
      </HStack>
    </Modal>
  );
}
