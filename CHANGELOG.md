<!--- BEGIN HEADER -->
# Changelog

All notable changes to this project will be documented in this file.
<!--- END HEADER -->

## [0.0.1](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/compare/3c6deb8eda675239ea9d85414d3638a698f3ae5f...v0.0.1) (2023-06-04)

### Features


##### Orders

* :sparkles: add actions enum and implement placeorder command and handler ([e836c1](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/e836c1b735eef964832e570f89458c1c6cb6f06d))
* :sparkles: calculating the total price of an order ([e532a5](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/e532a592b877ceea27fcbf1d6125c407da58c814))
* :sparkles: can calculate the total price ([fcfb3a](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/fcfb3a1a91ce8dad7108e4a041c8c7b89c4f602d))
* :sparkles: create new orders ([dc3db4](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/dc3db45477997e2022f9898bc6a082c3881eaf98))
* :sparkles: ensure can add product item correctly ([6268a0](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/6268a033108dee0a7fcc84a3eb352c03ee3764f0))
* :sparkles: ensure order can be marked as placed correctly ([7b6a2a](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/7b6a2aa3619073c056c7fcc8e7eca8b127656aa0))
* :sparkles: ensure order can initialized ([abd0dd](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/abd0dd3c4e4e6bfe232b31c9a6c3cdd57fc887b3))
* :sparkles: ensure remove a product item from the order correctly ([d1db6d](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/d1db6db97ae87c1717277e67a0681f32483c916b))
* :sparkles: fetch order ([caa966](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/caa966d0d72e11775c7662d47eb413f8a8751a32))
* :sparkles: include commands and handlers to add a product to an order ([275ef0](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/275ef05612b8cf0d85f07d8072d7117b4940751a))
* :sparkles: list product item from order ([e89305](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/e89305f9fdd4889f375deb77285f6784a1f7ae41))
* :sparkles: order factory ([fb77d5](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/fb77d543d5b22785a8a57c90d7b1c1846e5a7060))
* :sparkles: order status ([f5b1cd](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/f5b1cd2462974ac130414ebcedbc9c98dcf262c9))
* :sparkles: remove product from order ([8b707f](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/8b707f31a123f515fddedef7ae23cab95ecf2e89))

##### Products

* :sparkles: ensure product can be registered with events ([984b9c](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/984b9c0ff5005cae3f187848d420d8b1a1ef2d03))
* :sparkles: implement fetch product query and handler ([c558a1](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/c558a161b7f884cb1cfd7f13dda9d5c9335d35a9))
* :sparkles: product factory ([1c2492](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/1c2492f76c60602aa6889319716578747a9df598))
* :sparkles: register new products ([51ce53](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/51ce5362040804604fc55193f0d7df5870adbf95))

### Code Refactoring


##### Orders

* :fire: remove unnecessary code ([aaae41](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/aaae41387402a7dc8fbd088d0e7700a0a0e5162b))
* :recycle: adjust order-related code in application handlers ([2af4dc](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/2af4dc3ec9156691827a8e7c403ef7acef03a6c0))
* :recycle: change to create order process ([c15eb8](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/c15eb8faa73af263c21a884b26d1d6089f509c82))
* :recycle: fix variable type in createorder handler ([30406e](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/30406ef23db59a37d2f3eec3c6718d0c0f74fe8c))
* :recycle: merge order init tests ([50ea27](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/50ea27ca392addbdbb5dd3caadb6c629b883c03b))
* :recycle: simplify order constructor ([bedb6a](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/bedb6a63e6516b97aaff8c7dfc77ff6791837095))
* :rotating_light: fix warning when access undefined index ([83c0c3](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/83c0c34f3de619de3f5412c29b4e8931ebd7be6f))

##### Products

* :fire: remove unnecessary code ([48492f](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/48492fc24a5d8457cb8df70effeaa0b6d1f7c046))
* :label: change type of createAt attribute ([d0a373](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/d0a373fba44fc57f4000a1c0479a9ac45339550c))
* :recycle: change type of register product handler ([f12562](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/f12562d634e439473ad2c74a7decd97c28508905))
* :recycle: product fetch handling ([4d55e6](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/4d55e6774c9351d1cb954ec2aa20c9421056be74))

##### Project

* :recycle: remove final class statements ([928424](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/9284245b1732ac962e4bc2b4e5f771212ecf5be5))

### Styles


##### Project

* :art: linter ([fdb514](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/fdb5148af4e7e19113ad778301a9b0c75f09a1fb))
* :art: linting ([b1e940](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/b1e940ab33daf6e26c23964145b691e113c8b7b8))

### Tests


##### Orders

* :white_check_mark: add fetchorder unit tests ([0a6328](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/0a6328285b9d950977de5b958f2f8de3aef0aec5))
* :white_check_mark: ensure add product to order handler correctly ([2aabbc](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/2aabbc22a0a3adc180b43ebec1501c57bc2980b9))
* :white_check_mark: ensure can add product on order correctly ([ae5130](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/ae5130b24e66d120faab8ac27376f51a79e5a797))
* :white_check_mark: ensure can calculate the total price ([ca4058](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/ca4058e44f7ee7d099e4f02c287c84dd47bc77b7))
* :white_check_mark: ensure can list product order item with method ([106673](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/106673411748e4c6e3b10d39d4ffdabf30db5a14))
* :white_check_mark: ensure create new orders ([c6be8f](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/c6be8fc893bd30d72862f799a723ffa8081da5e2))
* :white_check_mark: ensure order can be calculated ([2a26b8](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/2a26b89bd39c70a38ad8fc8651301a4b33f1a35f))
* :white_check_mark: ensure order can be placed correctly ([8a7172](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/8a717245fad553983e6c1cbef1ae2f90812791d3))
* :white_check_mark: ensure order can initialized with events ([daacd4](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/daacd4dd485f3137d14dace6ac745e38a5150708))
* :white_check_mark: ensure order status is initialized when init a new order ([53d90d](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/53d90d435f04b60e5b47dab956db37a9dbaa3ced))
* :white_check_mark: ensure remove a product item from the order correctly ([a42f16](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/a42f165358740f6b61c1dc1570201c7b2506ce7d))
* :white_check_mark: ensure remove product from order ([f5500a](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/f5500afe81bf9f822a75e74a7a7faf9518ec406f))
* :white_check_mark: ensure than order factory create order correctly ([ba74f4](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/ba74f46d83f554f1b5b6459f9b0f2b04d393156b))
* :white_check_mark: unit tests for place order handler ([0c770c](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/0c770cda2c7ee6706ea41b0c3e59070248746630))

##### Products

* :white_check_mark: ensure fetch product query and handler results ([e340ff](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/e340ff047cf2e9a6a638dc0de641549db611af3a))
* :white_check_mark: ensure product can be registered with events ([56bec1](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/56bec1f0f9c99c1abae5bdae4963806b8762b6c0))
* :white_check_mark: ensure register new products ([09f8b3](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/09f8b38b34507b020cbf804ea4b3f10076f095f5))
* :white_check_mark: ensure than product factory create product correctly ([6823ca](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/6823ca92c8d5535f37411135b8d5cdd8783b5b70))

### Chores


##### Project

* :pushpin: phpunit on 9 version ([0b36b8](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/0b36b88dbedb3505d682797b6366f32d2d738f8e))
* :wrench: update settings of tools ([9dfa54](https://github.com/mateusmacedo/ddd-cqrs-eda-orders/commit/9dfa54d6e404f95a05e1f45e70250cde735c417d))


---

