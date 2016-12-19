# Changelog

All Notable changes to `json-guard-cli` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## 0.2.0 - 2016-12-19

### Changed

- Updated json-guard version to 0.5.1.  Command output was changed to match the new error format.
- If the value is longer than 100 characters it is truncated.

### Added
- Both check and validate can read data or the schema from STDIN, a file, a loader path, or a string.
- Now using a local copy of the draft 4 schema for the check command.
