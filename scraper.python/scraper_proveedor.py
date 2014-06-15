from scrapy.item import Item, Field

from scrapy.contrib.spiders import CrawlSpider, Rule
from scrapy.contrib.linkextractors.sgml import SgmlLinkExtractor
from scrapy.selector import Selector


class AdjudicacionItem(Item):
  lprv_id = Field()
  nit = Field()

  total_adjudicacion = Field()
  """tr.TablaFilaFooter"""




class ProveedorItem(Item):
  lprv_id = Field()
  """Campo lprv en la URL"""

  nombre = Field()
  """Nombre #MasterGC_ContentBlockHolder_lblNombreProv"""

  nit = Field()
  """#MasterGC_ContentBlockHolder_lblNIT"""

  #TODO: add support for multiple commercial names
  ## http://www.guatecompras.gt/proveedores/consultaProveeNomCom.aspx?rqp=8&lprv=32

  url = Field()


class ProveedorScraper(CrawlSpider):
    name = 'proveedoresGuateCompras'
    allowed_domains = ['guatecompras.gt']
    #start_urls = ['http://www.guatecompras.gt/proveedores/consultaProveeAdjLst.aspxy']
    start_urls = ["http://www.guatecompras.gt/proveedores/consultaDetProveeAdj.aspx?rqp=5&lprv=44331&iTipo=1"]
    rules = [Rule(SgmlLinkExtractor(allow=['/proveedores/consultaDetProvee.aspx/?lprv=\d+']), 'parse_proveedor')]

    def parse_adjetivos(self, response):
        pass

    def parse_proveedor(self, response):
        sel = Selector(response)
        proveedor = ProveedorItem()
        proveedor['url'] = response.url
        #print response.url
        #proveedor['lprv_id'] = response.url

        proveedor['nombre'] = sel.xpath("//span[@id='MasterGC_ContentBlockHolder_lblNombreProv']").extract()
        proveedor['nombre'] = sel.xpath("//span[@id='MasterGC_ContentBlockHolder_lblNIT']").extract()
        #torrent['size'] = sel.xpath("//div[@id='info-left']/p[2]/text()[2]").extract()
        #return torrent
        return proveedor
