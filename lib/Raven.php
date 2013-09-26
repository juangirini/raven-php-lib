<?php
/**
 * PHP class library for using the Raven Tools API
 *
 * API documentation:
 * https://api.raventools.com/docs/
 * 
 * The MIT License (MIT)
 * 
 * Copyright (c) 2012 Juan Girini
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * Rate Limiting: Each API key/IP address is allowed 100 requests per minute. 
 * If you exceed the limit, an HTTP 503 error response will be returned.
 *
 * @author  Juan Girini <juan.girini@gmail.com>
 * @link    https://github.com/juangirini/raven-php-lib
 * @version 1.0
 */
class Raven {

    private $key;
    private $uri;
    private $error;

    /**
     * Constructor
     * @param Array $config Must be array('key'=>RAVEN_API_KEY).    
     *                      Here is how you can get your RAVEN_API_KEY:
     *                      https://raven.zendesk.com/entries/243600-raven-api
     */
    function __construct($config) {
        $this->key = $config['key'];
        $this->uri = 'https://api.raventools.com/api?key=' . $this->key . '&format=json';
    }

    /**
     * Domain Keyword Rank. This request will return a list of matches for a 
     * particular domain, keyword, search engine, and date range. You can only 
     * access results for domains and keywords that have been added to your 
     * account, including competitor domains.
     * 
     * @param String    $domain     The domain name you want results for. Must match exactly.
     * @param String    $keyword    The keyword you want results for.
     * @param String    $start_date The beginning of the date range YYYY-MM-DD.
     * @param String    $end_date   The end of the date range YYYY-MM-DD.
     * @param String    $engine     The search engine you want results for. 
     *                              Available values are "all", "google", 
     *                              "yahoo", "msn" (aka bing), "google-uk", 
     *                              "google-au", "google-ca", "google-dk", 
     *                              "google-de", "google-no", "google-se", 
     *                              "google-pl", "google-fi", "google-fr", 
     *                              "google-es", "google-nl", "yahoo-ca", 
     *                              "yahoo-uk", "yahoo-au".
     * @return Mixed                Returns the result as an Array,
     *                              or false (Boolean) when it fails.
     */
    function rank($domain, $keyword, $start_date, $end_date, $engine) {
        $parameters = array(
            'method'        => __FUNCTION__,
            'domain'        => $domain,
            'keyword'       => $keyword,
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'engine'        => $engine
        );
        return $this->get_data($parameters);
    }
    
    /**
     * Domain All Keyword Rank. This request will return a list of matches for 
     * a particular domain and date. You can only access results for domains and
     * keywords that have been added to your account, including competitor domains.
     * 
     * @param   String  $domain     The domain name you want results for. Must match exactly.
     * @param   String  $start_date The date you want results for YYYY-MM-DD.
     * @return  Mixed               Returns the result as an Array,
     *                              or false (Boolean) when it fails.
     */
    function rank_all($domain, $start_date) {
        $parameters = array(
            'method'        => __FUNCTION__,
            'domain'        => $domain,
            'start_date'    => $start_date
        );
        return $this->get_data($parameters);
    }
    
    /**
     * Domains. This request will return the available domains for the profile 
     * associated with your API key.
     * 
     * @return  Mixed               Returns the result as an Array,
     *                              or false (Boolean) when it fails.
     */
    function domains() {
        $parameters = array(
            'method'        => __FUNCTION__
        );
        return $this->get_data($parameters);
    }
    
    /**
     * Domain Keyword Rank Maximum Week Available. This request returns the 
     * ISO Week number (YYYYWW) (http://en.wikipedia.org/wiki/ISO_week_date)
     * and date (YYYY-MM-DD) for the latest week with complete results for 
     * all keywords in a domain or a domain/keyword pair. 
     * It returns null for date/week and status = 'no data' for domains or 
     * domain/keywords that have no available data.
     * 
     * @param   String  $domain     The domain name you want results for. Must match exactly.
     * @param   String  $keyword    [optional] The keyword you want results for.
     * @return  Mixed               Returns the result as an Array,
     *                              or false (Boolean) when it fails.
     */
    function rank_max_week($domain, $keyword = null) {
        $parameters = array(
            'method'        => __FUNCTION__,
            'domain'        => $domain
        );
        
        if(!is_null($keyword))
            $parameters['keyword']=$keyword;
        
        return $this->get_data($parameters);
    }
    
    /**
     * Engines. This request will return the available search engines for 
     * tracking keywords, to be used when adding or modifying domains.
     * 
     * @return  Mixed   Returns the result as an Array,
     *                  or false (Boolean) when it fails.
     */
    function engines() {
        $parameters = array(
            'method'        => __FUNCTION__
        );
        return $this->get_data($parameters);
    }
    
    /**
     * Profile Info. This request will return the name and billable keyword 
     * usage for the current profile.
     * 
     * @return  Mixed   Returns the result as an Array,
     *                  or false (Boolean) when it fails.
     */
    function profile_info() {
        $parameters = array(
            'method'        => __FUNCTION__
        );
        return $this->get_data($parameters);
    }
    
    /**
     * Domain Info. This request will return the search engines for the domain provided.
     * 
     * @param   String  $domain The domain name you want results for. Must match exactly.
     * @return  Mixed           Returns the result as an Array,
     *                          or false (Boolean) when it fails.
     */
    function domain_info($domain) {
        $parameters = array(
            'method'        => __FUNCTION__,
            'domain'        => $domain
        );
        return $this->get_data($parameters);
    }
    
    /**
     * Remove Domain. This request will permanently remove the specified domain.
     * 
     * @param   String  $domain The domain name you want to remove - there is no undo.
     * @return  Boolean         Returns true when it success or false when it fails.
     */
    function remove_domain($domain) {
        $parameters = array(
            'method'        => __FUNCTION__,
            'domain'        => $domain
        );
        $data = $this->get_data($parameters);
        return (array_key_exists('response', $data) && $data['response']=="success");
    }
    
    /**
     * Add Domain. This request will add the domain provided.
     * 
     * @param   String  $domain     The domain name you want to add. "www." 
     *                              prefixes are ignored for purposes of 
     *                              matching ranks, but will be stored as part 
     *                              of the domain name for future requests.
     * @param   Array   $engine_ids List of search engine ids that you want to 
     *                              track for this domain. Available ids are 
     *                              returned by $this->engines()
     * @return  Boolean             Returns true when it success or false when it fails.
     */
    function add_domain($domain,$engine_ids) {
        $engine_ids_str='';
        
        foreach($engine_ids as $engine_id){
            $engine_ids_str.="$engine_id,";
        }
        
        $parameters = array(
            'method'        => __FUNCTION__,
            'domain'        => $domain,
            'engine_id'     => rtrim($engine_ids_str,",")
        );
        $data = $this->get_data($parameters);
        return (array_key_exists('response', $data) && $data['response']=="success");
    }
    
    /**
     * Add Keyword. This request will add keyword to the domain provided.
     * 
     * @param   String  $domain     The domain name you want to add a keyword to. 
     *                              Must match exactly.
     * @param   String  $keyword    The keyword name you want to add.
     * @return  Boolean             Returns true when it success or false when it fails.
     */
    function add_keyword($domain,$keyword) {
        $parameters = array(
            'method'        => __FUNCTION__,
            'domain'        => $domain,
            'keyword'       => $keyword
        );
        $data = $this->get_data($parameters);
        return (array_key_exists('response', $data) && $data['response']=="success");
    }
    
    /**
     * Remove Keyword. This request will remove a keyword from the domain provided.
     * 
     * @param   String  $domain     The domain name you want to remove 
     *                              the keyword from. Must match exactly.
     * @param   String  $keyword    The keyword name you want to remove.
     * @return  Boolean             Returns true when it success or false when it fails.
     */
    function remove_keyword($domain,$keyword) {
        $parameters = array(
            'method'        => __FUNCTION__,
            'domain'        => $domain,
            'keyword'       => $keyword
        );
        $data = $this->get_data($parameters);
        return (array_key_exists('response', $data) && $data['response']=="success");
    }
    
    /**
     * Competitors. This request will return the available competitors for the domain provided.
     * 
     * @param   String  $domain     The domain name you want results for. 
     *                              Must match exactly.
     * @return  Mixed               Returns the result as an Array,
     *                              or false (Boolean) when it fails.
     */
    function competitors($domain) {
        $parameters = array(
            'method'        => __FUNCTION__,
            'domain'        => $domain
        );
        return  $this->get_data($parameters);
    }
    
    /**
     * Keywords. This request will return the available keywords for the domain provided.
     * 
     * @param   String  $domain     The domain name you want results for. 
     *                              Must match exactly.
     * @return  Mixed               Returns the result as an Array,
     *                              or false (Boolean) when it fails.
     */
    function keywords($domain) {
        $parameters = array(
            'method'        => __FUNCTION__,
            'domain'        => $domain
        );
        return  $this->get_data($parameters);
    }
    
    /**
     * Keywords with Tags. This request will return the available keywords for the domain provided.
     * 
     * @param   String  $domain     The domain name you want results for. 
     *                              Must match exactly.
     * @return  Mixed               Returns the result as an Array,
     *                              or false (Boolean) when it fails.
     */
    function keywords_tags($domain) {
        $parameters = array(
            'method'        => __FUNCTION__,
            'domain'        => $domain
        );
        return  $this->get_data($parameters);
    }
    
    function upload_links(){
        /* TODO */
    }
    
    /**
     * Download Links. This request will return the all links for the domain provided.
     * 
     * @param   String  $domain     The domain name you want results for. 
     *                              Must match exactly.
     * @param   String  $tag        [optional] Filter your results to a particular tag
     * @return  Mixed               Returns the result as an Array,
     *                              or false (Boolean) when it fails.
     */
    function get_links($domain, $tag=null) {
        $parameters = array(
            'method'        => __FUNCTION__,
            'domain'        => $domain
        );
        
        if(!is_null($tag))
            $parameters['tag']=$tag;
        
        return  $this->get_data($parameters);
    }
    
    /**
     * Add Links. This request allows you to pass in an Associative Array with link data 
     * for the links you would like to create and returns a liew of new Link IDs.
     * 
     * @param   Array   $link       Associative Array that represents the link data you would like to 
     *                              create. Columns available are: 'domain',
     *                              'status','link type','link text','link url',
     *                              'link description', 'website name', 
     *                              'website url','website type','contact name', 
     *                              'contact email','content id','cost',
     *                              'cost type', 'payment method',
     *                              'payment reference','start date','end date', 
     *                              'creation date','comment','owner name','tags'.
     *                              E.G:
     *                              array(
     *                                  "domain"            => "raventools.com",
     *                                  "status"            => "active",
     *                                  "link text"         => "Raven Blog",
     *                                  "link url"          => "http://www.raventools.com/blog",
     *                                  "link description"  => "Raven Tools Blog"
     *                              ) 
     * @param   String  $domain     [optional] The domain name you want the 
     *                              links to be added under. This value is 
     *                              optional, it can be passed in on the 
     *                              individual link records as well, but must be 
     *                              passed in either here or on each link record. 
     * @return  Mixed               Returns the result as an Array,
     *                              or false (Boolean) when it fails.
     */
    function add_links($link,$domain = null) {
        $parameters = array(
            'method'    => __FUNCTION__,
            'link'      => json_encode($link)
        );
        
        if(!is_null($domain))
            $parameters['domain'] = $domain;
        
        return  $this->get_data($parameters);
    }
    
    /**
     * Update Links. This request allows you to pass in an Associative Array with link data 
     * for the links you would like to update and returns a list of the link ID's 
     * and if they were properly updated.
     *
     * @param   Array   $link       Associative Array that represents the link 
     *                              data you would like to update. 
     *                              Columns available are: 'domain','status',
     *                              'link id','link type','link text',
     *                              'link url','link description', 'website name', 
     *                              'website url','website type','contact name', 
     *                              'contact email','content id','cost','cost type', 
     *                              'payment method','payment reference',
     *                              'start date','end date', 'creation date',
     *                              'comment','owner name','tags'.
     *                              E.G:
     *                              array(
     *                                  "link id"           => "130",
     *                                  "status"            => "active",
     *                                  "link text"         => "Raven Blog",
     *                                  "link url"          => "www.raventools.com/blog",
     *                                  "link type"         => "Paid (Permanent)",
     *                                  "link description"  => "Raven Tools Blog",
     *                                  "website type"      => "sasdf",
     *                                  "website url"       => "www.about.com",
     *                                  "tags"              => "raven,blog",
     *                                  "creation date"     => "2012-07-14",
     *                                  "paymentmethod"     => "paypal",
     *                                  "cost"              => "12.45"
     *                              )
     * @param   String  $domain     [optional] The domain name you want the links 
     *                              to be updated under. This value is optional, 
     *                              it can be passed in on the individual 
     *                              link records as well, but must be passed in 
     *                              either here or on each link record.
     * @return  Mixed               Returns the result as an Array,
     *                              or false (Boolean) when it fails.
     */
    function update_links($link,$domain = null) {
        $parameters = array(
            'method'    => __FUNCTION__,
            'link'      => json_encode($link)
        );
        
        if(!is_null($domain))
            $parameters['domain'] = $domain;
        
        return  $this->get_data($parameters);
    }
    
    /**
     * Delete Links. This request allows you to pass in an Associative Array 
     * with link data for the links you would like to delete and returns a list 
     * of the link ID's and if they were properly deleted.
     *
     * @param   Array   $link       Associative Array that represents the link 
     *                              data you would like to delete. 
     *                              Columns available are: 'link id'
     *                              E.G:
     *                              array(
     *                                  array("link id" => "130"),
     *                                  array("link id" => "131"),
     *                                  array("link id" => "132")
     *                              )
     * @param   String  $domain     The domain name you want the links to be deleted from.
     * @return  Mixed               Returns the result as an Array,
     *                              or false (Boolean) when it fails.
     */
    function delete_links($link,$domain) {
        $parameters = array(
            'method'    => __FUNCTION__,
            'link'      => json_encode($link),
            'domain'    => $domain
        );
        
        return  $this->get_data($parameters);
    }
    
    /**
     *  Attempts authenticated GET Request.
     *
     *  @param  Array   $parameters Parameters for the GET request, with
     *                              the format: array('parameter'=>'value').
     *  @return Mixed               Curl result as an assosiative Array,
     *                              or false (Boolean) when it fails.
     */
    private function get_data($parameters) {
        $url = $this->uri;
        foreach ($parameters as $parameter => $value)
            $url.="&$parameter=$value";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, true);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        if($info['http_code'] != 200){
            $this->error=$info['http_code'];
            return false;
        }else{
            return json_decode($result,true);
        }
    }
    
    /**
     * In case that the connection with the Raven's server fails this function
     * returns the HTTP CODE for the error
     * 
     * @return integer 
     */
    public function get_error(){
        return $this->error;
    }

}
?>