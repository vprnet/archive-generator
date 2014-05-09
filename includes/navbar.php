<nav class="navbar navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <a id="link-home" class="navbar-brand" href="http://www.vpr.net">
                <div id="vpr-logo"></div>
            </a>
            <a href="index.html"><h1 id="navbar-title"><?php echo $this->title; ?></h1></a>

            <div id="social-logos">
                <a href="https://plus.google.com/share?url=<?=$social['url']?>" target="_blank">
                    <div id="google_icon"></div>
                </a>
                <a href="http://www.twitter.com/intent/tweet?url=<?=$social['url']?>&text=<?=$social['twitter_text']?>&via=vprnet&hashtags=<?=$social['hashtags']?>"
                   target="_blank">
                    <div id="twitter_icon"></div>
                </a>
                <a href="https://www.facebook.com/dialog/feed?app_id=282283015178&picture=<?=$social['image']?>&name=<?=$social['title']?>&description=<?=$social['description']?>&redirect_uri=<?=$social['url']?>"
                   target="_blank">
                    <div id="facebook_icon"></div>
                </a>
            </div>

        </div>
    </div>
</nav>
