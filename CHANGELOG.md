# Changelog

## 0.3.0 - 2017-04-30

- Updated to use json-guard 1.0.0 and json-reference 1.0.0
- Dropped 5.5 and HHVM support since json-guard dropped support
- Changed the validate headers from keyword, message, pointer, value to message, schema path, data path, cause.

## 0.2.1 - 2016-12-23

### Added

- The commands now return a non zero exit code for validation failures.

## 0.2.0 - 2016-12-19

### Changed

- Updated json-guard version to 0.5.1.  Command output was changed to match the new error format.
- If the value is longer than 100 characters it is truncated.

### Added
- Both check and validate can read data or the schema from STDIN, a file, a loader path, or a string.
- Now using a local copy of the draft 4 schema for the check command.
