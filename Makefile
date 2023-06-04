.PHONY: code-analyze
code-analyze:
	composer run-script code-analyze

.PHONY: code-analyze-report
code-analyze-report:
	composer run-script code-analyze-report

.PHONY: lint-fix
lint-fix:
	composer run-script lint-fix

.PHONY: test
test:
	composer run-script test

.PHONY: test-unit
test-unit:
	composer run-script test-unit

.PHONY: test-integration
test-integration:
	composer run-script test-integration

.PHONY: changelog
changelog:
	composer run-script changelog

.PHONY: release
release:
	composer run-script release

.PHONY: release-minor
release-minor:
	composer run-script release-minor

.PHONY: release-major
release-major:
	composer run-script release-major

.PHONY: release-patch
release-patch:
	composer run-script release-patch
