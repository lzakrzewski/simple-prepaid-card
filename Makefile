BUILD_DIR      = infrastructure/build
REPOSITORY_DIR = $(BUILD_DIR)/repository
REPOSITORY_URL = git@github.com:lzakrzewski/simple-prepaid-card.git
PACKAGE_DIR    = $(BUILD_DIR)/package

build_package:
	rm -rf $(REPOSITORY_DIR)
	rm -rf $(PACKAGE_DIR)
	mkdir -p $(REPOSITORY_DIR)
	mkdir -p $(PACKAGE_DIR)
	git clone --depth 1 $(REPOSITORY_URL) $(REPOSITORY_DIR)
	tar --directory $(REPOSITORY_DIR) -czf $(PACKAGE_DIR)/application.tar.gz .
	rm -rf $(REPOSITORY_DIR)

deploy: build_package
	ansible-playbook -i infrastructure/inventories/default/inventory infrastructure/deploy.yml