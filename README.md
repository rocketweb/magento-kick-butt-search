# magento-kick-butt-search
Improve Magento MySQL search by fixing some of the most common problems for many store owners.

Demo video: https://vimeo.com/56041912

Docs: http://www.rocketweb.com/documentation/KickButtSearch/readme.html

Search is one of the most highly used functions in an eCommerce site.  A good search engine tries to solve as much as it can through algorithms but also leaves a website owner with some tools to tweak results when necessary.  The Kick Butt Search extension from Rocket Web allows you to overcome some major hurdles in working with Magento search.

By default Magento search works like this:

- If a search term is contained in the name, description, short description, manufacturer or meta info all of those products will be returned and more or less sorted by the number of times the phrase appears in any field of those products.

-With URL rewrites you can correct specific searches and redirect them to search results for a different phrase.

- If you want to boost the importance of a specific product you need to see how people are searching for that product and repeatedly stuff those keywords into your product.

Kick Butt Search from Rocket Web helps like this:

- If a search phrase is in the name of your product, show that result before other products that may have the phrase in the description or other fields. If a customer searches for a sku, show that product first even if the sku is referenced in fields of other products.

- With Direct Search Queries specify a search phrase and exactly the products you want to come up for that phrase, including the sort order.

- Add a Search Boost to your most important products so that when all other factors are equal, your boosted products jump to the top of the search results.
- 

Version History

Version 2.0.8
Added search of skus in the products that compose a configurable, bundle or grouped product.
Added option to escape "-" or "+" characters.

Version 2.0.7
1.13 compatibility.
Minor bug fixes.

Version 2.0.6
Added support for searching categories.
Update direct search results to be able to provide categories, cms pages, blogs and categories as search results.

Version 2.0.3
Added support for searching CMS pages.
Added support to search blog posts created with the AheadWorks blog module.

Version 1.0.0
Place the products that have a title or sku match higher in the result list.
Manually add search boosts to specific products.
Define the exact result list for a specific search query.
Allowing you to redirect to a specific product detail page for a given search query.
