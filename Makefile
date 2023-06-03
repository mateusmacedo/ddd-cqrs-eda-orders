code-analyze:
	composer run-script code-analyze

code-analyze-report:
	composer run-script code-analyze-report

lint-fix:
	composer run-script lint-fix

test:
	composer run-script test

test-unit:
	composer run-script test-unit

test-integration:
	composer run-script test-integration

test-coverage:
	composer run-script test-coverage

test-coverage-all:
	composer run-script test-coverage-all

changelog:
	composer run-script changelog

release:
	composer run-script release

release-minor:
	composer run-script release-minor

release-major:
	composer run-script release-major

release-patch:
	composer run-script release-patch
