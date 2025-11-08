<?php
/*
** Application name: phpCollab
** Last Edit page: 04/12/2004
** Path by root: ../includes/calendar.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: calendar.php
**
** DESC: Screen:	calendar js file, this file is included every time a calendar js
**					popup is needed
**
** HISTORY:
** 	04/12/2004	-	added new document info
**	04/12/2004  -	fixed [ 1077236 ] Calendar bug in Client's Project site
**  2024-11-08  -   DEPRECATED: Replaced with native HTML5 <input type="date">
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/

/**
 * DEPRECATED: This file is no longer needed.
 *
 * As of Phase 3 modernization (November 2024), all calendar date pickers have been
 * replaced with native HTML5 <input type="date"> elements.
 *
 * The old jscalendar/dynarch library (2002-2005) has been replaced with:
 * - Native browser date pickers (no JavaScript required)
 * - Same YYYY-MM-DD format (no backend changes needed)
 * - Better accessibility and mobile support
 * - Elimination of 1806+ lines of legacy JavaScript
 *
 * This file is kept for backward compatibility but does nothing.
 * It can be safely removed in a future release.
 */

// Legacy variable kept for compatibility (no longer used)
$calendar_common_settings = "";
