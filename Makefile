cs:
	php-cs-fixer fix --verbose

cs_dry_run:
	php-cs-fixer fix --verbose --dry-run

test:
	phpunit

docs:
	cd docs && sphinx-build -W -b html -d _build/doctrees . _build/html
