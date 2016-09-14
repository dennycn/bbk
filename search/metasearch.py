#!/usr/bin/env python
#-*- coding: utf-8 -*-
'''
@desc: metasearch
@version: 1.0
@author: Denny
@date: 2016/6/18
'''

import sys
import re
import json
import urllib
import urllib2
from pyquery import PyQuery

# download_with_decode
#from spidermodule.util.download  import download_by_proxy
#from spidermodule.util.logsetting import logger

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
        print('download_with_decode: %s,%s',e,url)
        #logger.info('download_with_decode: %s,%s',e,url)
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
    _res = []
    def __init__(self):
        pass

    def search(self, query):
        res = []
        #logger.info('search: %s', query)
        try:
            self.query = query  #.decode('utf-8', 'ignore')
            self._url = self.build_url()
            print 46,self._url
            page = download_with_decode(self._url)
            #page = download_by_proxy(self._url, 'http://www.baidu.com/')
            #page = page.decode('utf-8', 'ignore')
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
            return []

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
            result_dict["url"] = url  #  #.encode('utf-8', 'ignore')
            result_dict["title"] = title  #  #.encode('utf-8', 'ignore')
            result_dict["snippet"] = abstract  #  #.encode('utf-8', 'ignore')
            result_dict["dispurl"] = url  #  #.encode('utf-8', 'ignore')
            ress.append(result_dict)

            print('77 %s %s %s' %(title, abstract, url))
        print('len(ress)=%d' %len(ress))
        return ress

class GoogleSearchEngine(SearchEngine):
    ''' GoogleSearchEngine
    '''
    def build_url(self):
        url = GOOGLEURL_PREFIX
        url += self.query
        url += GOOGLEURL_POSTFIX
        return url

    def parse_page(self, page):
        if not page:
            return []
        return []

class SogouSearchEngine(SearchEngine):
    '''
    '''
    def build_url(self):
        # https://www.sogou.com/web?query=mp3
        url = 'https://www.sogou.com/web?query='
        url += urlencode(self.query)
        return url

    def parse_page(self, page):
        if not page:
            return []

        print("pagelen=%d" %len(page))
        results = {}
        res_list = []
        dpage = PyQuery(page)
        datalist = dpage('.results div.rb')
        for item in datalist.items():
            ddpage = PyQuery(item.html())
            title = ddpage('h3.pt').text()
            abstract = ddpage('div.ft').text()
            url = ddpage('h3.pt a').attr('href')
            dis_url = ddpage('div.fb cite').text()
            res = SearchResult(title, abstract, url)
            result_dict = {}
            result_dict["url"] = url
            result_dict["title"] = title  #.encode('utf-8', 'ignore')
            result_dict["snippet"] = abstract  #.encode('utf-8', 'ignore')
            result_dict["dispurl"] = dis_url
            res_list.append(result_dict)

            #print('77 %s %s %s' %(title, abstract, url))
        results['data'] = res_list
        print('len(ress)=%d' %len(res_list))
        #return results
        return json.dumps(results)


def main_with_args():
    results = []
    se = None
    argv_num = len(sys.argv)
    if argv_num == 3:
        site = sys.argv[1]
        query = sys.argv[2]
        if site == 'baidu':
            se = BaiduSearchEngine()
        elif site == 'sogou':
            se = SogouSearchEngine()
        elif site == 'google':
            se = GoogleSearchEngine()

        results = se.search(query)
        print results
        return str(results)
    else:
        return 'Usage: ' + sys.argv[0] + ' [site] [query] '


#if __name__ == "__main__":
    #pass
main_with_args()
