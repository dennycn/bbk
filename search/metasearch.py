#!/usr/bin/env python
#-*- coding: utf-8 -*-
'''
@desc: metasearch
@version: 1.0
@author: Denny
@date: 2016/6/18
'''

import sys
import urllib
from pyquery import PyQuery

#from spidermodule.util.download  import download_with_decode, download_by_proxy

CHARSET = 'utf-8'

# 只获取网页结果：新闻/资讯
GOOGLEURL_PREFIX = u"http://www.google.com.hk/#hl=zh-CN&source=hp&q="
GOOGLEURL_POSTFIX = ""
BAIDUURL_PREFIX = u"http://www.baidu.com/s?wd="
BAIDUURL_POSTFIX = u"&cl=2"   # cl: 2/3~news
BAIDUURL_SEARCH = u'http://www.baidu.com/s?'

def urlencode(val):
    if isinstance(val,unicode):
        val = str(val)
    return urllib.quote_plus(val)

def download_with_decode(url):
    ''' download_with_decode(): get method
        download url by urlopen and decode
    '''
    result = None
    response = None
    try :
        header = {'User-Agent':'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11', \
'Accept':'text/html;q=0.9,*/*;q=0.8',\
#'Accept-Charset':'ISO-8859-1,utf-8;q=0.7,*;q=0.3',\
}
        request = urllib2.Request(url, None, header)
        response = urllib2.urlopen(request, None, timeout=10)
        html = response.read()

        # get charset
        charset = 'utf-8'  # default charset
        charsets = response.headers['Content-Type'].split(' charset=')
        if len(charsets) > 1 :
            #charset = response.headers.getparam('charset')
            charset=charsets[1].lower()
        else:
            charsets = re.search(ur'<meta\s*http-equiv="?Content-Type"? content="text/html;\s*charset=([\w\d-]+?)"', html, re.IGNORECASE)
            if charsets:
                charset=charsets.group(1)
        #print 237,'charset=', charset
        result=html.decode(charset, 'ignore')
    except urllib2.URLError, reason:
        raise urllib2.URLError(reason)
    except Exception,e:
        logger.error('download_with_decode: %s,%s',e,url)
    finally:
        if response:
            response.close()

    return result


class SearchResult:
    '''
    '''
    def __init__(self, title, abstract, url):
        self.title = title
        self.abstract = abstract
        self.url = url
    title = ''
    abstract = ''
    url = ''


class SearchEngine:
    ''' SearchEngine
    '''
    _query = ''
    _url = ''
    _res = {}
    def __init__(self):
        pass

    def search(self, query):
        res = None
        try:
            self.query = query  #.decode(CHARSET, 'ignore')
            self._url = self.build_url()
            print 46,self._url
            page = download_with_decode(self._url)
            #page = download_by_proxy(self._url, 'http://www.baidu.com/')

            res = self.parse_page(page)
        except Exception,e:   #
            print('search error: %s %s', e, query)
        return res

    def build_url(self):
        pass

    def parse_page(self, page):
        pass


class BaiduSearchEngine(SearchEngine):
    ''' BaiduSearchEngine
    '''
    def build_url(self):
        url = BAIDUURL_PREFIX
        url += self.query
        url += BAIDUURL_POSTFIX
#        param = {'cl':2, 'wd':self.query}
#        url = BAIDUURL_SEARCH + urllib.urlencode(param)
        return url

    def parse_page(self, page):
        if not page:
            return None

        print("pagelen=%d" %len(page))
        ress = []
        dpage = PyQuery(page)
        datalist = dpage('.result')

        for item in datalist.items():
            ddpage = PyQuery(item.html())
            title = ddpage('h3.t').text()
            abstract = ddpage('').text()
            url = ddpage('div.f13 a.c-showurl').text()
            res = SearchResult(title, abstract, url)
            result_dict = {}
            result_dict['url'] = url
            result_dict['title'] = title
            result_dict['snippet'] = abstract
            result_dict['dispurl'] = url
            ress.append(result_dict)

            print('77 %s %s %s' %(title, abstract, url))
        print('len(ress)=%d' %len(ress))
        return ress

class GoogleSearchEngine(SearchEngine):
    ''' GoogleSearchEngine
    '''
    def build_url(self):
        url = GOOGLEURL_PREFIX
        url += urllib.quote(self.query)
        url += GOOGLEURL_POSTFIX
        return url

    def download_page(self, url):
        pass

    def parse_page(self, page):
        pass


class SogouSearchEngine(SearchEngine):
    '''
    '''
    pass


#if __name__ == "__main__":
argv_num = len(sys.argv)
if argv_num == 2:
    query = sys.argv[1]
    se = BaiduSearchEngine()
    res = se.search(query)
    #res = se.search(u'mp4')
    print(res)
else: print None