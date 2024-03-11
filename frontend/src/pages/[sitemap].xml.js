const sitemapBuilder = async (sitemap) => {
  const res = await fetch(`${import.meta.env.API_URL}/${sitemap}.xml`)
  let sitemapData = await res.text()

  if (sitemap === 'sitemap_index') {
    sitemapData = sitemapData.replace(/\/es\//g, '/')
  }
  return sitemapData
}

export const get = async ({ params }) => {
  const sitemap = await sitemapBuilder(params.sitemap)

  return new Response(sitemap, {
    status: 200,
    headers: {
      'Content-Type': 'text/xml; charset=UTF-8'
    }
  })
}
