# JavaScript Tests

Unit tests for phpCollab JavaScript files using Jest.

## Setup (One-time)

```bash
npm install
```

This installs Jest and related testing dependencies. **NOT required for production builds.**

## Running Tests

```bash
# Run all tests
npm test

# Run tests in watch mode (re-runs on file changes)
npm run test:watch

# Run with coverage report
npm run test:coverage
```

## Current Test Coverage

- `general.test.js` - Tests for javascript/general.js (checkbox management, utilities)

**Note:** These are demonstration/placeholder tests. Full test coverage would require modularizing the JavaScript files to export functions properly.

## Future Testing Improvements

For comprehensive testing, consider:

1. **Modularize JavaScript**
   - Convert to ES6 modules (`export`/`import`)
   - Use a bundler (webpack/rollup) for production builds
   - Makes testing much easier

2. **Expand Test Coverage**
   - Test all MM_* functions individually
   - Test event delegation system
   - Test tooltip behavior
   - Test form validation

3. **Integration Tests**
   - Test actual DOM interactions
   - Test with real forms and checkboxes
   - Test accessibility features

4. **E2E Tests**
   - Consider Playwright/Cypress for full browser tests
   - Test actual user workflows

## Philosophy

These tests are **optional development tools** to ensure code quality. They are NOT required for:
- Production builds
- Releases
- Normal phpCollab operation

Use them during development to catch bugs early and ensure modernization efforts maintain functionality.
