<?php
//currently images don't autodownload while scraping

require_once('simple_html_dom.php');

class vpr_app
{

    public $config;

    public function __construct()
    {
        $this->config = parse_ini_file("config.ini");
        foreach ($this->config as $k => $v) {
            $this->$k = $v;
        }
    }

//loads stories from a single html file
    public function load_from_file($file, $category)
    {
        $r = array();
        $html = file_get_html($file);
        foreach ($html->find('.story') as $s) {
            $r['title'] = $s->find('.title', 0)->plaintext;
            $r['date'] = $s->find('.date', 0)->plaintext;
            $r['time'] = strtotime($r['date']);
            $r['text'] = $s->find('.text', 0)->innertext;
            $r['byline'] = $s->find('.byline', 0)->innertext;
            $r['images'] = array();
            $r['category'] = $category;
            $r['file'] = preg_replace('/\PL/u', '', $r['title']) . ".html";
            $r['audio']['title'] = "test";
            $r['audio']['url'] = $s->find('.audio', 0)->innertext;
            $r['audio']['file'] = "./audio/" . basename($r['audio']['url']);
            $r['isSingle'] = FALSE;
            $r['id'] = $s->find('.id', 0)->plaintext;
            if (strlen($r['text']) > 2) {
                $this->posts[] = $r;
            }
        }
    }

//loads stories from a directory of individual page exports
    public function load_from_directory($search_dirs, $category)
    {
        $files = array();
        foreach ($search_dirs as $directory) {
            foreach (new DirectoryIterator($directory) as $file) {
                if ($file->isFile()) {
                    $temp = array();
                    $temp['file'] = file_get_contents($directory . $file);
                    $temp['fullpath'] = $directory . $file;
                    $temp['name'] = $file->getFileName();
                    $files[] = $temp;
                }
            }
        }
        foreach ($files as $file) {
            $r = array();
            $html = str_get_html($file['file']);
            $post_header = $html->find('.post_header', 0);
            $r['title'] = $post_header->find('h3', 0)->plaintext;
            $r['date'] = $post_header->find('.post_time', 0)->plaintext;
            $r['time'] = strtotime($r['date']);
            $r['text'] = $html->find('.post_body', 0)->innertext;
            $r['images'] = array();
            $r['fullpath'] = $file['fullpath'];
            $temp = explode('.', $file['name']);
            $r['file'] = $temp[0];
            $r['category'] = $category;
            $r['byline'] = "Steve Zind";
            $r['isSingle'] = FALSE;
            $this->posts[] = $r;
        }
    }

//download image from url NOT WORKING
    function download_image($name)
    {
        echo "Downloading " . $url . "<br>";
        $url = 'http://www.vpr.net/uploads/photos/original/' . $name . '.jpg';
        $data = $this->get_data($url);
        file_put_contents($name, $data);
    }

//download audio from url
    function download_audio($url, $output)
    {
        echo "Downloading " . $url . "<br>";
        $data = $this->get_data($url);
        $name = basename($url);
        file_put_contents($output . "/" . $name, $data);
    }

//fetch data from url
    function get_data($url)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

//get excerpt from text
    function excerpt($content, $maxchars)
    {
        if (strlen($content) < $maxchars) {
            return $content;
        } else {
            //
            $pos = iconv_strpos($content, ".");
            if ($pos > $maxchars) {
                $content = substr($content, 0, $pos + 1);
            } else {
                $temp = str_split($content, $maxchars);
                $pos = iconv_strpos($temp[1], ".");
                $content = substr($content, 0, $pos + strlen($temp[0]) + 1);
            }
            return $content;
        }
    }

//sort by key value
    function aasort(&$array, $key)
    {
        $sorter = array();
        $ret = array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii] = $va[$key];
        }
        asort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii] = $array[$ii];
        }
        $array = $ret;
        $array = array_reverse($array);
    }

//set attr
    public function set($prop, $val)
    {
        $this->$prop = $val;
    }

//get attr
    public function get($prop)
    {
        return $this->$prop . "<br />";
    }

//create the index
    function create_index()
    {

        $title = $this->config['title'];
        $social['title'] = $this->config['social_title'];
        $social['description'] = $this->config['social_description'];
        $social['url'] = $this->config['root_url'];
        $social['image'] = $this->config['root_url'].$this->config['social_image'];
        $social['hashtags'] = $this->config['social_hashtags'];
        $social['twitter_text'] = $this->config['social_twitter_text'];
        $this->aasort($this->posts, "time");
        $data = "";
        ob_start();
        include('/includes/header.php');
        $data .= ob_get_clean();
        foreach ($this->posts as $post) {
            // echo "TEXT:" . $post['text'] . "<br><br><br>";
            $html = str_get_html($post['text']);
            $post['image'] = $html->find('img', 0)->src;
            $text = $this->excerpt(strip_tags($post['text']), 350) . "<p><span class=\"file glyphicon glyphicon-file\"></span> <a href=\"" . $post['file'] . "\">Read More</a></p>";
            $post['text'] = preg_replace("/&#?nsbp+;/i", "", $text);
            $post['landscape'] = FALSE;
            if (isset($post['image'])) {
                $image_size = getimagesize("./output/image/" . basename($post['image']));
                if ($image_size[0] > 500 AND $image_size[1] < 600) {
                    $post['landscape'] = TRUE;
                }
            }
            ob_start();
            include('/includes/story.php');
            $data .= ob_get_clean();
        }
        ob_start();
        include('/includes/sidebar.php');
        include('/includes/footer.php');
        $data .= ob_get_clean();
        $data = $this->fix_images($data);
        $fp = fopen($this->output_directory . "index.html", 'w');
        fwrite($fp, $this->format_html($data));
        fclose($fp);
        echo "index.html";
    }

    function format_html($html)
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadHTML($html);
        $dom->formatOutput = TRUE;
        return $dom->saveHTML();
    }

//create individual pages
    function create_pages()
    {

        $this->aasort($this->posts, "time");
        for ($i = 0; $i < count($this->posts); $i++) {
            if (!isset($this->posts[$i]['id'])) {
                $post = $this->posts[$i];
                $html = str_get_html($this->posts[$i]['text']);
                foreach ($html->find('img') as $image) {
                    $pic = basename($image->src);
                    $image->src = "./image/" . $pic;
                    $r['images'][] = $image->src;
                }
                $posts['images'] = $r['images'];
                foreach ($html->find('a') as $a) {
                    if ($a->href == "http://www.vpr.net/news/afghanistan/index.php") {
                        $a->innertext = "";
                    }
                }
                $post['text'] = $html->save();
                $post['text'] = str_replace("Visit The Series Page:", "", $post['text']);
                $post['text'] = str_replace("<h2>", "", $post['text']);
                $post['text'] = str_replace("</h2>", "", $post['text']);
                $social['title'] = $this->posts[$i]['title'];
                $social['description'] = strip_tags($this->excerpt($this->posts[$i]['text'], 350));
                $social['url'] = $this->posts[$i]['file'];
                $social['image'] = $this->posts[$i]['images'];
                $post['isSingle'] = TRUE;
                $p = $i - 1;
                if ($p < 0) {
                    $p = 0;
                }
                $n = $i + 1;
                if ($n > count($this->posts)) {
                    $n = count($this->posts);
                }
                $post['previous_story_url'] = $this->posts[$n]['file'];
                $post['next_story_url'] = $this->posts[$p]['file'];
                ob_start();
                include('/includes/header.php');
                include('/includes/story.php');
                include('/includes/sidebar.php');
                include('/includes/footer.php');
                $data = ob_get_clean();
                $fp = fopen($this->output_directory . $this->posts[$i]['file'], 'w');
                fwrite($fp, $this->format_html($data));
                fclose($fp);
                echo $this->posts[$i]['file'] . "</br>";
            }
        }
    }

    //replace the directory of all images

    function fix_images($html_string)
    {
        $html = str_get_html($html_string);
        foreach ($html->find('img') as $image) {
            $pic = basename($image->data_original);
            $image->data_original = "./image/" . $pic;
            $r['images'][] = $image->data_original;
        }


        return $html->save();
    }
}