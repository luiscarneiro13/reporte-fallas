// Category display order — populated by wp_localize_script() from the PHP
// get_categories() method in class-sydney-pattern-library.php.
export const TYPE_ORDER = window.sydneyPatternLibrary?.typeOrder || [];

// Maximum number of recently-used patterns kept in the client-side LRU.
// One screen of pattern cards in the grid. Server-side cap is higher
// (RECENTLY_USED_SERVER_CAP = 50 in PHP) so future bumps don't need a
// migration.
export const RECENTLY_USED_MAX = 12;
