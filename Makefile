test:
	phpunit
	cd docs && sphinx-build -W -b html -d _build/doctrees . _build/html

bower:
	/usr/local/node/node-v0.10.22/bin/bower update