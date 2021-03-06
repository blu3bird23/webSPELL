<?php
/*
##########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2015 by webspell.org                                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org                                                   #
#                                                                        #
##########################################################################
*/

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}
if (isset($_REQUEST[ 'quickactiontype' ])) {
    $quickactiontype = $_REQUEST[ 'quickactiontype' ];
} else {
    $quickactiontype = '';
}

if ($action == "new") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('news');
    $_language->readModule('bbcode', true);
    if (!isnewswriter($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    safe_query(
        "INSERT INTO
            " . PREFIX . "news (
                date,
                poster,
                saved
            )
            VALUES (
                '" . time() . "',
                '" . $userID . "',
                '0'
                )"
    );
    $newsID = mysqli_insert_id($_database);

    $rubrics = '';
    $newsrubrics = safe_query("SELECT rubricID, rubric FROM " . PREFIX . "news_rubrics ORDER BY rubric");
    while ($dr = mysqli_fetch_array($newsrubrics)) {
        $rubrics .= '<option value="' . $dr[ 'rubricID' ] . '">' . $dr[ 'rubric' ] . '</option>';
    }

    if (isset($_POST[ 'topnews' ])) {
        safe_query("UPDATE " . PREFIX . "settings SET topnewsID='$newsID'");
    }

    $count_langs = 0;
    $lang = safe_query("SELECT lang, language FROM " . PREFIX . "news_languages ORDER BY language");
    $langs = '';
    while ($dl = mysqli_fetch_array($lang)) {
        $langs .= "news_languages[" . $count_langs . "] = new Array();\nnews_languages[" . $count_langs . "][0] = '" .
            $dl[ 'lang' ] . "';\nnews_languages[" . $count_langs . "][1] = '" . $dl[ 'language' ] . "';\n";
        $count_langs++;
    }

    $message_vars = '';
    $headline_vars = '';
    $langs_vars = '';
    $langcount = 1;

    $url1 = "http://";
    $url2 = "http://";
    $url3 = "http://";
    $url4 = "http://";
    $link1 = '';
    $link2 = '';
    $link3 = '';
    $link4 = '';
    $window1 = '<input class="input" name="window1" type="checkbox" value="1">';
    $window2 = '<input class="input" name="window2" type="checkbox" value="1">';
    $window3 = '<input class="input" name="window3" type="checkbox" value="1">';
    $window4 = '<input class="input" name="window4" type="checkbox" value="1">';

    $intern = '<option value="0" selected="selected">' . $_language->module[ 'no' ] . '</option><option value="1">' .
        $_language->module[ 'yes' ] . '</option>';
    $topnews = '<option value="0" selected="selected">' . $_language->module[ 'no' ] . '</option><option value="1">' .
        $_language->module[ 'yes' ] . '</option>';

    $bg1 = BG_1;

    $selects = '';
    for ($i = 1; $i <= $count_langs; $i++) {
        $selects .= '<option value="' . $i . '">' . $i . '</option>';
    }

    $tags = '';

    $postform = '';
    $comments = '<option value="0">' . $_language->module[ 'no_comments' ] . '</option><option value="1">' .
        $_language->module[ 'user_comments' ] . '</option><option value="2" selected="selected">' .
        $_language->module[ 'visitor_comments' ] . '</option>';

    $componentsCss = generateComponents($components['css'], 'css');
    $componentsJs = generateComponents($components['js'], 'js');

    $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
    $addflags = $GLOBALS["_template"]->replaceTemplate("flags", array());

    $data_array = array();
    $data_array['$rewriteBase'] = $rewriteBase;
    $data_array['$componentsCss'] = $componentsCss;
    $data_array['$addbbcode'] = $addbbcode;
    $data_array['$addflags'] = $addflags;
    $data_array['$rubrics'] = $rubrics;
    $data_array['$newsID'] = $newsID;
    $data_array['$topnews'] = $topnews;
    $data_array['$intern'] = $intern;
    $data_array['$tags'] = $tags;
    $data_array['$langcount'] = $langcount;
    $data_array['$link1'] = $link1;
    $data_array['$url1'] = $url1;
    $data_array['$window1'] = $window1;
    $data_array['$link2'] = $link2;
    $data_array['$url2'] = $url2;
    $data_array['$window2'] = $window2;
    $data_array['$link3'] = $link3;
    $data_array['$url3'] = $url3;
    $data_array['$window3'] = $window3;
    $data_array['$link4'] = $link4;
    $data_array['$url4'] = $url4;
    $data_array['$window4'] = $window4;
    $data_array['$userID'] = $userID;
    $data_array['$comments'] = $comments;
    $data_array['$selects'] = $selects;
    $data_array['$message_vars'] = $message_vars;
    $data_array['$headline_vars'] = $headline_vars;
    $data_array['$langs_vars'] = $langs_vars;
    $data_array['$langs'] = $langs;
    $data_array['$componentsJs'] = $componentsJs;
    $news_post = $GLOBALS["_template"]->replaceTemplate("news_post", $data_array);
    echo $news_post;
} elseif ($action == "save") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('news');
    $newsID = $_POST[ 'newsID' ];

    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT
                poster
            FROM
                " . PREFIX . "news
            WHERE
                newsID = '" . (int)$newsID ."'"
        )
    );
    if (($ds[ 'poster' ] != $userID || !isnewswriter($userID)) && !isnewsadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    $save = isset($_POST[ 'save' ]);
    $preview = isset($_POST[ 'preview' ]);

    if (isset($_POST[ 'rubric' ])) {
        $rubric = $_POST[ 'rubric' ];
    } else {
        $rubric = 0;
    }

    $lang = $_POST[ 'lang' ];
    $headline = $_POST[ 'headline' ];
    $message = $_POST[ 'message' ];
    $message = str_replace('\r\n', "\n", $message);

    $link1 = strip_tags($_POST[ 'link1' ]);
    $url1 = strip_tags($_POST[ 'url1' ]);
    $window1 = $_POST[ 'window1' ];

    $link2 = strip_tags($_POST[ 'link2' ]);
    $url2 = strip_tags($_POST[ 'url2' ]);
    $window2 = $_POST[ 'window2' ];

    $link3 = strip_tags($_POST[ 'link3' ]);
    $url3 = strip_tags($_POST[ 'url3' ]);
    $window3 = $_POST[ 'window3' ];

    $link4 = strip_tags($_POST[ 'link4' ]);
    $url4 = strip_tags($_POST[ 'url4' ]);
    $window4 = $_POST[ 'window4' ];

    $intern = $_POST[ 'intern' ];
    $comments = $_POST[ 'comments' ];

    safe_query(
        "UPDATE
            " . PREFIX . "news
        SET
            rubric='" . $rubric . "',
            link1='" . $link1 . "',
            url1='" . $url1 . "',
            window1='" . $window1 . "',
            link2='" . $link2 . "',
            url2='" . $url2 . "',
            window2='" . $window2 . "',
            link3='" . $link3 . "',
            url3='" . $url3 . "',
            window3='" . $window3 . "',
            link4='" . $link4 . "',
            url4='" . $url4 . "',
            window4='" . $window4 . "',
            saved='1',
            intern='" . $intern . "',
            comments='" . $comments . "'
        WHERE
            newsID='" . (int)$newsID . "'"
    );

    \webspell\Tags::setTags('news', $newsID, $_POST[ 'tags' ]);

    $update_langs = array();
    $query = safe_query("SELECT language FROM " . PREFIX . "news_contents WHERE newsID = '" . (int)$newsID ."'");
    while ($qs = mysqli_fetch_array($query)) {
        $update_langs[ ] = $qs[ 'language' ];
        if (in_array($qs[ 'language' ], $lang)) {
            $update_langs[ ] = $qs[ 'language' ];
        } else {
            safe_query(
                "DELETE FROM
                " . PREFIX . "news_contents
                WHERE
                    `newsID` = '" . $newsID . "' AND
                    `language` = '" . $qs[ 'language' ] ."'"
            );
        }
    }

    $counter = count($message);
    for ($i = 0; $i < $counter; $i++) {
        if (in_array($lang[ $i ], $update_langs)) {
            safe_query(
                "UPDATE
                    " . PREFIX . "news_contents
                SET
                    `headline` = '" . $headline[ $i ] . "',
                    `content` = '" . $message[ $i ] . "'
                WHERE
                    `newsID` = '" . $newsID . "' AND
                    `language` = '" . $lang[ $i ]."'"
            );
            unset($update_langs[ $lang[ $i ] ]);
        } else {
            safe_query(
                "INSERT INTO
                    " . PREFIX . "news_contents (
                        `newsID`,
                        `language`,
                        `headline`,
                        `content`
                    )
                    VALUES (
                        '" . $newsID . "',
                        '" . $lang[ $i ] . "',
                        '" . $headline[ $i ] . "',
                        '" . $message[ $i ]."'
                    )"
            );
        }
    }

    // delete the entries that are older than 2 hour and contain no text
    safe_query("DELETE FROM `" . PREFIX . "news` WHERE `saved` = '0' and " . time() . " - `date` > " . (2 * 60 * 60));

    if (isset($_POST[ 'topnews' ])) {
        if ($_POST[ 'topnews' ]) {
            safe_query("UPDATE " . PREFIX . "settings SET topnewsID='" . $newsID . "'");
        } elseif (!$_POST[ 'topnews' ] && $newsID == $topnewsID) {
            safe_query("UPDATE " . PREFIX . "settings SET topnewsID='0'");
        }
    }
    generate_rss2();
    if ($save) {
        echo '<body onload="window.close()"></body>';
    }
    if ($preview) {
        header("Location: news.php?action=preview&newsID=" . $newsID);
    }
    if ($languagecount) {
        header("Location: news.php?action=edit&newsID=" . $newsID);
    }
} elseif ($action == "preview") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('news');

    $newsID = $_GET[ 'newsID' ];

    $result = safe_query("SELECT * FROM " . PREFIX . "news WHERE newsID='$newsID'");
    $ds = mysqli_fetch_array($result);

    if (($ds[ 'poster' ] != $userID || !isnewswriter($userID)) && !isnewsadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="description" content="Clanpage using webSPELL 4 CMS">
    <meta name="author" content="webspell.org">
    <meta name="copyright" content="Copyright 2005-2015 by webspell.org">
    <meta name="generator" content="webSPELL">

    <!-- Head & Title include -->
    <title>' . PAGETITLE . '; ?></title>
    <base href="'.$rewriteBase.'">';
    foreach ($components['css'] as $component) {
        echo '<link href="' . $component . '" rel="stylesheet">';
    }
    echo '<script src="js/bbcode.js"></script>
    <!-- end Head & Title include -->
</head>
<body>';

    $bg1 = BG_1;

    $title_news = $GLOBALS["_template"]->replaceTemplate("title_news", array());
    echo $title_news;

    $bgcolor = BG_1;
    $date = getformatdate($ds[ 'date' ]);
    $time = getformattime($ds[ 'date' ]);
    $rubrikname = getrubricname($ds[ 'rubric' ]);
    $rubrikname_link = getinput(getrubricname($ds[ 'rubric' ]));
    $rubricpic = '<img src="images/news-rubrics/' . getrubricpic($ds[ 'rubric' ]) . '" class="img-responsive" alt="">';
    if (!file_exists($rubricpic)) {
        $rubricpic = '';
    }

    $adminaction = '';

    $message_array = array();
    $query = safe_query("SELECT * FROM " . PREFIX . "news_contents WHERE newsID='" . $newsID . "'");
    while ($qs = mysqli_fetch_array($query)) {
        $message_array[ ] =
            array('lang' => $qs[ 'language' ], 'headline' => $qs[ 'headline' ], 'message' => $qs[ 'content' ]);
    }
    $showlang = select_language($message_array);

    $langs = '';
    $i = 0;
    foreach ($message_array as $val) {
        if ($showlang != $i) {
            $langs .=
                '<a href="index.php?site=news_comments&amp;newsID=' . $ds[ 'newsID' ] . '&amp;lang=' . $val[ 'lang' ] .
                '">[flag]' . $val[ 'lang' ] . '[/flag]</a>';
        }
        $i++;
    }
    $langs = flags($langs);

    $headline = $message_array[ $showlang ][ 'headline' ];
    $content = $message_array[ $showlang ][ 'message' ];

    if ($ds[ 'intern' ] == 1) {
        $isintern = '(' . $_language->module[ 'intern' ] . ')';
    } else {
        $isintern = '';
    }

    $content = htmloutput($content);
    $content = toggle($content, $ds[ 'newsID' ]);
    $poster = '<a href="index.php?site=profile&amp;id=' . $ds[ 'poster' ] . '"><b>' . getnickname($ds[ 'poster' ]) .
        '</b></a>';
    $related = '';
    $comments = "";
    if ($ds[ 'link1' ] && $ds[ 'url1' ] != "http://" && $ds[ 'window1' ]) {
        $related .= '&#8226; <a href="' . $ds[ 'url1' ] . '" target="_blank">' . $ds[ 'link1' ] . '</a> ';
    }
    if ($ds[ 'link1' ] && $ds[ 'url1' ] != "http://" && !$ds[ 'window1' ]) {
        $related .= '&#8226; <a href="' . $ds[ 'url1' ] . '">' . $ds[ 'link1' ] . '</a> ';
    }

    if ($ds[ 'link2' ] && $ds[ 'url2' ] != "http://" && $ds[ 'window2' ]) {
        $related .= '&#8226; <a href="' . $ds[ 'url2' ] . '" target="_blank">' . $ds[ 'link2' ] . '</a> ';
    }
    if ($ds[ 'link2' ] && $ds[ 'url2' ] != "http://" && !$ds[ 'window2' ]) {
        $related .= '&#8226; <a href="' . $ds[ 'url2' ] . '">' . $ds[ 'link2' ] . '</a> ';
    }

    if ($ds[ 'link3' ] && $ds[ 'url3' ] != "http://" && $ds[ 'window3' ]) {
        $related .= '&#8226; <a href="' . $ds[ 'url3' ] . '" target="_blank">' . $ds[ 'link3' ] . '</a> ';
    }
    if ($ds[ 'link3' ] && $ds[ 'url3' ] != "http://" && !$ds[ 'window3' ]) {
        $related .= '&#8226; <a href="' . $ds[ 'url3' ] . '">' . $ds[ 'link3' ] . '</a> ';
    }

    if ($ds[ 'link4' ] && $ds[ 'url4' ] != "http://" && $ds[ 'window4' ]) {
        $related .= '&#8226; <a href="' . $ds[ 'url4' ] . '" target="_blank">' . $ds[ 'link4' ] . '</a> ';
    }
    if ($ds[ 'link4' ] && $ds[ 'url4' ] != "http://" && !$ds[ 'window4' ]) {
        $related .= '&#8226; <a href="' . $ds[ 'url4' ] . '">' . $ds[ 'link4' ] . '</a> ';
    }

    $tags = \webspell\Tags::getTagsLinked('news', $ds[ 'newsID' ]);

    $data_array = array();
    $data_array['$newsID'] = $newsID;
    $data_array['$headline'] = $headline;
    $data_array['$rubrikname'] = $rubrikname;
    $data_array['$rubricpic'] = $rubricpic;
    $data_array['$isintern'] = $isintern;
    $data_array['$content'] = $content;
    $data_array['$adminaction'] = $adminaction;
    $data_array['$poster'] = $poster;
    $data_array['$date'] = $date;
    $data_array['$comments'] = $comments;
    $news = $GLOBALS["_template"]->replaceTemplate("news", $data_array);
    echo $news;

    echo '<hr>
    <a href="news.php?action=edit&amp;newsID=' . $newsID . '" class="btn btn-danger">' .
        $_language->module[ 'edit' ] . '</a>
    <input type="button" onclick="javascript:self.close()" value="' .
        $_language->module[ 'save_news' ] . '" class="btn btn-danger">
    <input type="button" onclick="MM_confirm(
        \'' . $_language->module[ 'really_delete' ] .
        '\', \'news.php?action=delete&amp;id=' . $newsID . '&amp;close=true\'
    )" value="' . $_language->module[ 'delete' ] . '" class="btn btn-danger"></body></html>';
} elseif ($quickactiontype == "publish") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('news');
    if (!isnewsadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    if (isset($_POST[ 'newsID' ])) {
        $newsID = $_POST[ 'newsID' ];
        if (is_array($newsID)) {
            foreach ($newsID as $id) {
                safe_query("UPDATE " . PREFIX . "news SET published='1' WHERE newsID='" . (int)$id . "'");
            }
        } else {
            safe_query("UPDATE " . PREFIX . "news SET published='1' WHERE newsID='" . (int)$newsID . "'");
        }
        generate_rss2();
        header("Location: index.php?site=news");
    } else {
        header("Location: index.php?site=news&action=unpublished");
    }
} elseif ($quickactiontype == "unpublish") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('news');
    if (!isnewsadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    if (isset($_REQUEST[ 'newsID' ])) {
        $newsID = $_REQUEST[ 'newsID' ];
        if (is_array($newsID)) {
            foreach ($newsID as $id) {
                safe_query("UPDATE " . PREFIX . "news SET published='0' WHERE newsID='" . (int)$id . "'");
            }
        } else {
            safe_query("UPDATE " . PREFIX . "news SET published='0' WHERE newsID='" . (int)$newsID . "'");
        }
        generate_rss2();
    }
    header("Location: index.php?site=news");
} elseif ($quickactiontype == "delete") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('news');
    if (isset($_POST[ 'newsID' ])) {
        $newsID = $_POST[ 'newsID' ];

        foreach ($newsID as $id) {
            $ds = mysqli_fetch_array(
                safe_query(
                    "SELECT
                        screens,
                        poster
                    FROM
                        " . PREFIX . "news
                    WHERE
                        newsID='" . (int)$id ."'"
                )
            );
            if (($ds[ 'poster' ] != $userID || !isnewswriter($userID)) && !isnewsadmin($userID)) {
                die($_language->module[ 'no_access' ]);
            }
            if ($ds[ 'screens' ]) {
                $screens = explode("|", $ds[ 'screens' ]);
                if (is_array($screens)) {
                    $filepath = "./images/news-pics/";
                    foreach ($screens as $screen) {
                        if (file_exists($filepath . $screen)) {
                            @unlink($filepath . $screen);
                        }
                    }
                }
            }
            \webspell\Tags::removeTags('news', $id);
            safe_query("DELETE FROM " . PREFIX . "news WHERE newsID='" . (int)$id ."'");
            safe_query("DELETE FROM " . PREFIX . "news_contents WHERE newsID='" . (int)$id ."'");
            safe_query("DELETE FROM " . PREFIX . "comments WHERE parentID='" . (int)$id . "' AND type='ne'");
        }
        generate_rss2();
        header("Location: index.php?site=news&action=archive");
    } else {
        generate_rss2();
        header("Location: index.php?site=news&action=archive");
    }
} elseif ($action == "delete") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('news');

    $id = $_GET[ 'id' ];

    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT
                screens, poster
            FROM
                " . PREFIX . "news
            WHERE
                newsID='" . (int)$id ."'"
        )
    );
    if (($ds[ 'poster' ] != $userID || !isnewswriter($userID)) && !isnewsadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }
    if ($ds[ 'screens' ]) {
        $screens = explode("|", $ds[ 'screens' ]);
        if (is_array($screens)) {
            $filepath = "./images/news-pics/";
            foreach ($screens as $screen) {
                if (file_exists($filepath . $screen)) {
                    @unlink($filepath . $screen);
                }
            }
        }
    }

    \webspell\Tags::removeTags('news', $id);

    safe_query("DELETE FROM " . PREFIX . "news WHERE newsID='" . (int)$id ."'");
    safe_query("DELETE FROM " . PREFIX . "news_contents WHERE newsID='" . (int)$id ."'");
    safe_query("DELETE FROM " . PREFIX . "comments WHERE parentID='" . (int)$id . "' AND type='ne'");

    generate_rss2();
    if (isset($_GET[ 'close' ])) {
        echo '<body onload="window.close()"></body>';
    } else {
        header("Location: index.php?site=news");
    }
} elseif ($action == "edit") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('news');

    $newsID = $_GET[ 'newsID' ];

    $ds = mysqli_fetch_array(safe_query("SELECT * FROM " . PREFIX . "news WHERE newsID='" . $newsID . "'"));
    if (($ds[ 'poster' ] != $userID || !isnewswriter($userID)) && !isnewsadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    $_language->readModule('bbcode', true);

    $message_array = array();
    $query = safe_query("SELECT * FROM " . PREFIX . "news_contents WHERE newsID='" . $newsID . "'");
    while ($qs = mysqli_fetch_array($query)) {
        $message_array[ ] =
            array('lang' => $qs[ 'language' ], 'headline' => $qs[ 'headline' ], 'message' => $qs[ 'content' ]);
    }

    $count_langs = 0;
    $lang = safe_query("SELECT lang, language FROM " . PREFIX . "news_languages ORDER BY language");
    $langs = '';
    while ($dl = mysqli_fetch_array($lang)) {
        $langs .= "news_languages[" . $count_langs . "] = new Array();\nnews_languages[" . $count_langs . "][0] = '" .
            $dl[ 'lang' ] . "';\nnews_languages[" . $count_langs . "][1] = '" . $dl[ 'language' ] . "';\n";
        $count_langs++;
    }

    $message_vars = '';
    $headline_vars = '';
    $langs_vars = '';
    $i = 0;
    foreach ($message_array as $val) {
        $message_vars .= "message[" . $i . "] = '" . js_replace($val[ 'message' ]) . "';\n";
        $headline_vars .= "headline[" . $i . "] = '" . js_replace(htmlspecialchars($val[ 'headline' ])) . "';\n";
        $langs_vars .= "langs[" . $i . "] = '" . $val[ 'lang' ] . "';\n";
        $i++;
    }
    $langcount = $i;

    $newsrubrics = safe_query("SELECT * FROM " . PREFIX . "news_rubrics ORDER BY rubric");
    $rubrics = '';
    while ($dr = mysqli_fetch_array($newsrubrics)) {
        if ($ds[ 'rubric' ] == $dr[ 'rubricID' ]) {
            $rubrics .= '<option value="' . $dr[ 'rubricID' ] . '" selected="selected">' . getinput($dr[ 'rubric' ]) .
                '</option>';
        } else {
            $rubrics .= '<option value="' . $dr[ 'rubricID' ] . '">' . getinput($dr[ 'rubric' ]) . '</option>';
        }
    }

    if ($ds[ 'intern' ]) {
        $intern =
            '<option value="0">' . $_language->module[ 'no' ] . '</option><option value="1" selected="selected">' .
            $_language->module[ 'yes' ] . '</option>';
    } else {
        $intern =
            '<option value="0" selected="selected">' . $_language->module[ 'no' ] . '</option><option value="1">' .
            $_language->module[ 'yes' ] . '</option>';
    }
    if ($topnewsID == $newsID) {
        $topnews =
            '<option value="0">' . $_language->module[ 'no' ] . '</option><option value="1" selected="selected">' .
            $_language->module[ 'yes' ] . '</option>';
    } else {
        $topnews =
            '<option value="0" selected="selected">' . $_language->module[ 'no' ] . '</option><option value="1">' .
            $_language->module[ 'yes' ] . '</option>';
    }

    $selects = '';
    for ($i = 1; $i <= $count_langs; $i++) {
        if ($i == $langcount) {
            $selects .= '<option value="' . $i . '" selected="selected">' . $i . '</option>';
        } else {
            $selects .= '<option value="' . $i . '">' . $i . '</option>';
        }
    }

    $link1 = getinput($ds[ 'link1' ]);
    $link2 = getinput($ds[ 'link2' ]);
    $link3 = getinput($ds[ 'link3' ]);
    $link4 = getinput($ds[ 'link4' ]);

    $url1 = "http://";
    $url2 = "http://";
    $url3 = "http://";
    $url4 = "http://";

    if ($ds[ 'url1' ] != "http://") {
        $url1 = $ds[ 'url1' ];
    }
    if ($ds[ 'url2' ] != "http://") {
        $url2 = $ds[ 'url2' ];
    }
    if ($ds[ 'url3' ] != "http://") {
        $url3 = $ds[ 'url3' ];
    }
    if ($ds[ 'url4' ] != "http://") {
        $url4 = $ds[ 'url4' ];
    }

    if ($ds[ 'window1' ]) {
        $window1 = '<input class="input" name="window1" type="checkbox" value="1" checked="checked">';
    } else {
        $window1 = '<input class="input" name="window1" type="checkbox" value="1">';
    }

    if ($ds[ 'window2' ]) {
        $window2 = '<input class="input" name="window2" type="checkbox" value="1" checked="checked">';
    } else {
        $window2 = '<input class="input" name="window2" type="checkbox" value="1">';
    }

    if ($ds[ 'window3' ]) {
        $window3 = '<input class="input" name="window3" type="checkbox" value="1" checked="checked">';
    } else {
        $window3 = '<input class="input" name="window3" type="checkbox" value="1">';
    }

    if ($ds[ 'window4' ]) {
        $window4 = '<input class="input" name="window4" type="checkbox" value="1" checked="checked">';
    } else {
        $window4 = '<input class="input" name="window4" type="checkbox" value="1">';
    }

    $tags = \webspell\Tags::getTags('news', $newsID);

    $comments = '<option value="0">' . $_language->module[ 'no_comments' ] . '</option><option value="1">' .
        $_language->module[ 'user_comments' ] . '</option><option value="2">' .
        $_language->module[ 'visitor_comments' ] . '</option>';
    $comments =
        str_replace(
            'value="' . $ds[ 'comments' ] . '"',
            'value="' . $ds[ 'comments' ] . '" selected="selected"',
            $comments
        );

    $bg1 = BG_1;

    $componentsCss = generateComponents($components['css'], 'css');
    $componentsJs = generateComponents($components['js'], 'js');

    $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
    $addflags = $GLOBALS["_template"]->replaceTemplate("flags", array());

    $data_array = array();
    $data_array['$rewriteBase'] = $rewriteBase;
    $data_array['$componentsCss'] = $componentsCss;
    $data_array['$addbbcode'] = $addbbcode;
    $data_array['$addflags'] = $addflags;
    $data_array['$rubrics'] = $rubrics;
    $data_array['$newsID'] = $newsID;
    $data_array['$topnews'] = $topnews;
    $data_array['$intern'] = $intern;
    $data_array['$tags'] = $tags;
    $data_array['$langcount'] = $langcount;
    $data_array['$link1'] = $link1;
    $data_array['$url1'] = $url1;
    $data_array['$window1'] = $window1;
    $data_array['$link2'] = $link2;
    $data_array['$url2'] = $url2;
    $data_array['$window2'] = $window2;
    $data_array['$link3'] = $link3;
    $data_array['$url3'] = $url3;
    $data_array['$window3'] = $window3;
    $data_array['$link4'] = $link4;
    $data_array['$url4'] = $url4;
    $data_array['$window4'] = $window4;
    $data_array['$userID'] = $userID;
    $data_array['$comments'] = $comments;
    $data_array['$selects'] = $selects;
    $data_array['$message_vars'] = $message_vars;
    $data_array['$headline_vars'] = $headline_vars;
    $data_array['$langs_vars'] = $langs_vars;
    $data_array['$langs'] = $langs;
    $data_array['$componentsJs'] = $componentsJs;
    $news_post = $GLOBALS["_template"]->replaceTemplate("news_post", $data_array);
    echo $news_post;
} elseif (basename($_SERVER[ 'PHP_SELF' ]) == "news.php") {
    generate_rss2();
    header("Location: index.php?site=news");
} elseif ($action == "unpublished") {
    $_language->readModule('news');

    $title_news = $GLOBALS["_template"]->replaceTemplate("title_news", array());
    echo $title_news;

    $post = '';
    if (isnewsadmin($userID)) {
        $post =
            '<input type="button" onclick="window.open(
                    \'news.php?action=new\',
                    \'News\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\'
                );" value="' . $_language->module[ 'post_news' ] . '" class="btn btn-danger">';
    }

    echo $post .
        ' <a href="index.php?site=news" class="btn btn-danger">' . $_language->module[ 'show_news' ] . '</a><hr>';

    $page = '';

    // Not published News
    if (isnewsadmin($userID)) {
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "news WHERE published='0' AND saved='1' ORDER BY date ASC");
        if (mysqli_num_rows($ergebnis)) {
            echo $_language->module[ 'title_unpublished_news' ];

            echo '<form method="post" name="form" action="news.php">';
            $news_unpublished_head = $GLOBALS["_template"]->replaceTemplate("news_unpublished_head", array());
            echo $news_unpublished_head;

            $i = 1;
            while ($ds = mysqli_fetch_array($ergebnis)) {
                if ($i % 2) {
                    $bg1 = BG_1;
                    $bg2 = BG_2;
                } else {
                    $bg1 = BG_3;
                    $bg2 = BG_4;
                }

                $date = getformatdate($ds[ 'date' ]);
                $rubric = getrubricname($ds[ 'rubric' ]);
                if (!isset($rubric)) {
                    $rubric = '';
                }
                $comms = getanzcomments($ds[ 'newsID' ], 'ne');
                $message_array = array();
                $query = safe_query("SELECT * FROM " . PREFIX . "news_contents WHERE newsID='" . $ds[ 'newsID' ] . "'");
                while ($qs = mysqli_fetch_array($query)) {
                    $message_array[ ] =
                        array(
                            'lang' => $qs[ 'language' ],
                            'headline' => $qs[ 'headline' ],
                            'message' => $qs[ 'content' ]
                        );
                }

                $headlines = '';

                foreach ($message_array as $val) {
                    $headlines .= '<a href="index.php?site=news_comments&amp;newsID=' . $ds[ 'newsID' ] . '&amp;lang=' .
                        $val[ 'lang' ] . '">' . flags('[flag]' . $val[ 'lang' ] . '[/flag]') . ' ' .
                        clearfromtags($val[ 'headline' ]) . '</a><br>';
                }

                $poster =
                    '<a href="index.php?site=profile&amp;id=' . $ds[ 'poster' ] . '">' . getnickname($ds[ 'poster' ]) .
                    '</a>';

                $multiple = '';
                $admdel = '';
                if (isnewsadmin($userID)) {
                    $multiple = '<input class="input" type="checkbox" name="newsID[]" value="' . $ds[ 'newsID' ] . '">';
                    $admdel = '<div class="row">
                        <div class="col-md-6">
                            <input class="input" type="checkbox" name="ALL" value="ALL"
                                onclick="SelectAll(this.form);"> ' . $_language->module[ 'select_all' ] . '
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <select name="quickactiontype" class="form-control">
                                    <option value="publish">' . $_language->module[ 'publish_selected' ] . '</option>
                                    <option value="delete">' . $_language->module[ 'delete_selected' ] . '</option>
                                </select>
                                <span class="input-group-btn">
                                    <input type="submit" name="quickaction" value="' . $_language->module[ 'go' ] . '"
                                        class="btn btn-danger">
                                </span>
                            </div>
                        </div>
                    </div></form>';
                }

                $data_array = array();
                $data_array['$multiple'] = $multiple;
                $data_array['$date'] = $date;
                $data_array['$rubric'] = $rubric;
                $data_array['$headlines'] = $headlines;
                $news_archive_content = $GLOBALS["_template"]->replaceTemplate("news_archive_content", $data_array);
                echo $news_archive_content;
                $i++;
            }
            $data_array = array();
            $data_array['$admdel'] = $admdel;
            $news_archive_foot = $GLOBALS["_template"]->replaceTemplate("news_archive_foot", $data_array);
            echo $news_archive_foot;

            unset($ds);
        }
    }
} elseif ($action == "archive") {
    $_language->readModule('news');

    $title_news = $GLOBALS["_template"]->replaceTemplate("title_news", array());
    echo $title_news;

    if (isset($_GET[ 'page' ])) {
        $page = (int)$_GET[ 'page' ];
    } else {
        $page = 1;
    }
    $sort = "date";
    if (isset($_GET[ 'sort' ])) {
        if (($_GET[ 'sort' ] == 'date') || ($_GET[ 'sort' ] == 'poster') || ($_GET[ 'sort' ] == 'rubric')) {
            $sort = $_GET[ 'sort' ];
        }
    }

    $type = "DESC";
    if (isset($_GET[ 'type' ])) {
        if (($_GET[ 'type' ] == 'ASC') || ($_GET[ 'type' ] == 'DESC')) {
            $type = $_GET[ 'type' ];
        }
    }

    $post = '';
    $publish = '';
    if (isnewsadmin($userID)) {
        $post =
            '<input type="button" onclick="window.open(
                    \'news.php?action=new\',
                    \'News\',
                    \'toolbar=no,status=no,scrollbars=yes,width=800,height=600\'
                )"
                value="' . $_language->module[ 'post_news' ] . '" class="btn btn-danger">';
        $unpublished = safe_query("SELECT newsID FROM " . PREFIX . "news WHERE published='0' AND saved='1'");
        $unpublished = mysqli_num_rows($unpublished);
        if ($unpublished) {
            $publish =
                '<a href="index.php?site=news&amp;action=unpublished" class="btn btn-danger">' .
                    $unpublished . ' ' . $_language->module[ 'unpublished_news' ] . '
                </a>';
        }
    }
    echo $post . ' ' . $publish .
        ' <a href="index.php?site=news" class="btn btn-primary">' . $_language->module[ 'show_news' ] . '</a><hr>';

    $all = safe_query(
        "SELECT
            newsID
        FROM
            " . PREFIX . "news
        WHERE
            published='1' AND
            intern<=" . (int)isclanmember($userID)
    );
    $gesamt = mysqli_num_rows($all);
    $pages = 1;

    $max = empty($maxnewsarchiv) ? 20 : $maxnewsarchiv;
    $pages = ceil($gesamt / $max);

    if ($pages > 1) {
        $page_link =
            makepagelink(
                "index.php?site=news&amp;action=archive&amp;sort=" . $sort . "&amp;type=" . $type,
                $page,
                $pages
            );
    } else {
        $page_link = '';
    }

    if ($page == "1") {
        $ergebnis = safe_query(
            "SELECT
                *
            FROM
                " . PREFIX . "news
            WHERE
                published='1' AND
                intern<=" . (int)isclanmember($userID) . "
            ORDER BY
                " . $sort . " " . $type . "
            LIMIT 0," . (int)$max
        );
        if ($type == "DESC") {
            $n = $gesamt;
        } else {
            $n = 1;
        }
    } else {
        $start = $page * $max - $max;
        $ergebnis = safe_query(
            "SELECT
                *
            FROM
                " . PREFIX . "news
            WHERE
                published='1' AND
                intern<=" . (int)isclanmember($userID) . "
            ORDER BY
                " . $sort . " " . $type . "
            LIMIT " . (int)$start . "," . (int)$max
        );
        if ($type == "DESC") {
            $n = ($gesamt) - $page * $max + $max;
        } else {
            $n = ($gesamt + 1) - $page * $max + $max;
        }
    }
    if ($all) {
        if ($type == "ASC") {
            echo '<a href="index.php?site=news&amp;action=archive&amp;page=' . $page . '&amp;sort=' . $sort .
                '&amp;type=DESC">' . $_language->module[ 'sort' ] .
                '</a> <span class="glyphicon glyphicon-chevron-down"></span>&nbsp;&nbsp;&nbsp;';
        } else {
            echo '<a href="index.php?site=news&amp;action=archive&amp;page=' . $page . '&amp;sort=' . $sort .
                '&amp;type=ASC">' . $_language->module[ 'sort' ] .
                '</a> <span class="glyphicon glyphicon-chevron-up"></span>&nbsp;&nbsp;&nbsp;';
        }

        if ($pages > 1) {
            echo $page_link;
        }
        if (isnewsadmin($userID)) {
            echo '<form method="post" name="form" action="news.php">';
        }

        $data_array = array();
        $data_array['$page'] = $page;
        $news_archive_head = $GLOBALS["_template"]->replaceTemplate("news_archive_head", $data_array);
        echo $news_archive_head;

        $i = 1;
        while ($ds = mysqli_fetch_array($ergebnis)) {
            if ($i % 2) {
                $bg1 = BG_1;
                $bg2 = BG_2;
            } else {
                $bg1 = BG_3;
                $bg2 = BG_4;
            }

            $date = getformatdate($ds[ 'date' ]);
            $rubric = getrubricname($ds[ 'rubric' ]);
            $comms = getanzcomments($ds[ 'newsID' ], 'ne');
            if ($ds[ 'intern' ] == 1) {
                $isintern = '<small>(' . $_language->module[ 'intern' ] . ')</small>';
            } else {
                $isintern = '';
            }

            $message_array = array();
            $query = safe_query("SELECT * FROM " . PREFIX . "news_contents WHERE newsID='" . $ds[ 'newsID' ] . "'");
            while ($qs = mysqli_fetch_array($query)) {
                $message_array[ ] =
                    array('lang' => $qs[ 'language' ], 'headline' => $qs[ 'headline' ], 'message' => $qs[ 'content' ]);
            }

            $headlines = '';

            foreach ($message_array as $val) {
                $headlines .= '<a href="index.php?site=news_comments&amp;newsID=' . $ds[ 'newsID' ] . '&amp;lang=' .
                    $val[ 'lang' ] . '">' . flags('[flag]' . $val[ 'lang' ] . '[/flag]') . ' ' .
                    clearfromtags($val[ 'headline' ]) . '</a> ' . $isintern . '<br>';
            }

            $poster =
                '<a href="index.php?site=profile&amp;id=' . $ds[ 'poster' ] . '">' . getnickname($ds[ 'poster' ]) .
                '</a>';

            $multiple = '';
            $admdel = '';
            if (isnewsadmin($userID)) {
                $multiple =
                    '<input class="archiveitem-checkb" type="checkbox" name="newsID[]" value="' . $ds[ 'newsID' ] .
                    '">';
            }

            $data_array = array();
            $data_array['$multiple'] = $multiple;
            $data_array['$date'] = $date;
            $data_array['$rubric'] = $rubric;
            $data_array['$headlines'] = $headlines;
            $news_archive_content = $GLOBALS["_template"]->replaceTemplate("news_archive_content", $data_array);
            echo $news_archive_content;
            $i++;
        }

        if (isnewsadmin($userID)) {
            $admdel = '<div class="row">

                <div class="col-md-4">
                    <input class="input" id="archivecbx" type="checkbox" name="ALL" value="ALL"
                    onclick="SelectAll(this.form);">
                    <label for="archivecbx">' . $_language->module[ 'select_all' ] . '</label>
                </div>

                <div class="col-md-8">
                    <div class="input-group">
                        <select name="quickactiontype" class="form-control">
                        <option value="delete">' . $_language->module[ 'delete_selected' ] . '</option>
                        <option value="unpublish">' . $_language->module[ 'unpublish_selected' ] . '</option>
                    </select>

                    <span class="input-group-btn">
                    <input type="submit" name="quickaction" value="' .
                    $_language->module[ 'go' ] . '" class="btn btn-danger">
                    </span>
                </div>

                </div>
            </div></form>';
        } else {
            $admdel = '';
        }

        $data_array = array();
        $data_array['$admdel'] = $admdel;
        $news_archive_foot = $GLOBALS["_template"]->replaceTemplate("news_archive_foot", $data_array);
        echo $news_archive_foot;
        unset($ds);
    } else {
        echo 'no entries';
    }
} else {
    $_language->readModule('news');

    $title_news = $GLOBALS["_template"]->replaceTemplate("title_news", array());
    echo $title_news;

    $post = '';
    $publish = '';
    if (isnewswriter($userID)) {
        $post =
            '<input type="button"
                onclick="window.open(
                    \'news.php?action=new\',
                    \'News\',
                    \'toolbar=no,status=no,scrollbars=yes,width=800,height=600\'
                );" value="' . $_language->module[ 'post_news' ] . '" class="btn btn-danger">';
    }
    if (isnewsadmin($userID)) {
        $unpublished = safe_query("SELECT newsID FROM " . PREFIX . "news WHERE published='0' AND saved='1'");
        $unpublished = mysqli_num_rows($unpublished);
        if ($unpublished) {
            $publish =
                '<a href="index.php?site=news&amp;action=unpublished" class="btn btn-danger">' .
                $unpublished . ' ' . $_language->module[ 'unpublished_news' ] . '</a>';
        }
    }
    echo $post . ' ' . $publish .
        '<a href="index.php?site=news&amp;action=archive" class="btn btn-primary">' .
        $_language->module[ 'news_archive' ] . '</a><hr>';

    if (isset($_GET[ 'show' ])) {
        $result = safe_query(
            "SELECT
                rubricID
            FROM
                " . PREFIX . "news_rubrics
            WHERE
                rubric='" . $_GET[ 'show' ] . "'
            LIMIT 0,1"
        );
        $dv = mysqli_fetch_array($result);
        $showonly = "AND rubric='" . $dv[ 'rubricID' ] . "'";
    } else {
        $showonly = '';
    }

    $result =
        safe_query(
            "SELECT
                *
            FROM
                " . PREFIX . "news
            WHERE
                published='1' AND
                intern<=" . (int)isclanmember($userID) . " " . $showonly . "
            ORDER BY
                date DESC
            LIMIT 0," . (int)$maxshownnews
        );

    $i = 1;
    while ($ds = mysqli_fetch_array($result)) {
        if ($i % 2) {
            $bg1 = BG_1;
        } else {
            $bg1 = BG_2;
        }

        $date = getformatdate($ds[ 'date' ]);
        $time = getformattime($ds[ 'date' ]);
        $rubrikname = getrubricname($ds[ 'rubric' ]);
        $rubrikname_link = getinput($rubrikname);
        $rubricpic_path = "images/news-rubrics/" . getrubricpic($ds[ 'rubric' ]);
        $rubricpic = '<img src="' . $rubricpic_path . '" alt="">';
        if (!is_file($rubricpic_path)) {
            $rubricpic = '';
        }

        $message_array = array();
        $query = safe_query("SELECT * FROM " . PREFIX . "news_contents WHERE newsID='" . (int)$ds[ 'newsID' ] . "'");
        while ($qs = mysqli_fetch_array($query)) {
            $message_array[ ] =
                array('lang' => $qs[ 'language' ], 'headline' => $qs[ 'headline' ], 'message' => $qs[ 'content' ]);
        }

        $showlang = select_language($message_array);

        $langs = '';
        $x = 0;
        foreach ($message_array as $val) {
            if ($showlang != $x) {
                $langs .= '<span style="padding-left:2px">
                    <a href="index.php?site=news_comments&amp;newsID=' . $ds[ 'newsID' ] . '&amp;lang=' .
                        $val[ 'lang' ] . '">
                        [flag]' . $val[ 'lang' ] . '[/flag]
                    </a>
                </span>';
            }
            $x++;
        }
        $langs = flags($langs);

        $headline = $message_array[ $showlang ][ 'headline' ];
        $content = $message_array[ $showlang ][ 'message' ];
        $newsID = $ds[ 'newsID' ];
        if ($ds[ 'intern' ] == 1) {
            $isintern = '(' . $_language->module[ 'intern' ] . ')';
        } else {
            $isintern = '';
        }

        $content = htmloutput($content);
        $content = toggle($content, $ds[ 'newsID' ]);
        $headline = clearfromtags($headline);
        $poster = '<a href="index.php?site=profile&amp;id=' . $ds[ 'poster' ] . '"><b>' . getnickname($ds[ 'poster' ]) .
            '</b></a>';
        $related = "";
        if ($ds[ 'link1' ] && $ds[ 'url1' ] != "http://" && $ds[ 'window1' ]) {
            $related .= '&#8226; <a href="' . $ds[ 'url1' ] . '" target="_blank">' . $ds[ 'link1' ] . '</a> ';
        }
        if ($ds[ 'link1' ] && $ds[ 'url1' ] != "http://" && !$ds[ 'window1' ]) {
            $related .= '&#8226; <a href="' . $ds[ 'url1' ] . '">' . $ds[ 'link1' ] . '</a> ';
        }

        if ($ds[ 'link2' ] && $ds[ 'url2' ] != "http://" && $ds[ 'window2' ]) {
            $related .= '&#8226; <a href="' . $ds[ 'url2' ] . '" target="_blank">' . $ds[ 'link2' ] . '</a> ';
        }
        if ($ds[ 'link2' ] && $ds[ 'url2' ] != "http://" && !$ds[ 'window2' ]) {
            $related .= '&#8226; <a href="' . $ds[ 'url2' ] . '">' . $ds[ 'link2' ] . '</a> ';
        }

        if ($ds[ 'link3' ] && $ds[ 'url3' ] != "http://" && $ds[ 'window3' ]) {
            $related .= '&#8226; <a href="' . $ds[ 'url3' ] . '" target="_blank">' . $ds[ 'link3' ] . '</a> ';
        }
        if ($ds[ 'link3' ] && $ds[ 'url3' ] != "http://" && !$ds[ 'window3' ]) {
            $related .= '&#8226; <a href="' . $ds[ 'url3' ] . '">' . $ds[ 'link3' ] . '</a> ';
        }

        if ($ds[ 'link4' ] && $ds[ 'url4' ] != "http://" && $ds[ 'window4' ]) {
            $related .= '&#8226; <a href="' . $ds[ 'url4' ] . '" target="_blank">' . $ds[ 'link4' ] . '</a> ';
        }
        if ($ds[ 'link4' ] && $ds[ 'url4' ] != "http://" && !$ds[ 'window4' ]) {
            $related .= '&#8226; <a href="' . $ds[ 'url4' ] . '">' . $ds[ 'link4' ] . '</a> ';
        }

        if (empty($related)) {
            $related = "n/a";
        }

        if ($ds[ 'comments' ]) {
            if ($ds[ 'cwID' ]) {
                // CLANWAR-NEWS
                $anzcomments = getanzcomments($ds[ 'cwID' ], 'cw');
                $replace = array('$anzcomments', '$url', '$lastposter', '$lastdate');
                $vars = array(
                    $anzcomments,
                    'index.php?site=clanwars_details&amp;cwID=' . $ds[ 'cwID' ],
                    clearfromtags(getlastcommentposter($ds[ 'cwID' ], 'cw')),
                    getformatdatetime(getlastcommentdate($ds[ 'cwID' ], 'cw'))
                );

                switch ($anzcomments) {
                    case 0:
                        $comments = str_replace($replace, $vars, $_language->module[ 'no_comment' ]);
                        break;
                    case 1:
                        $comments = str_replace($replace, $vars, $_language->module[ 'comment' ]);
                        break;
                    default:
                        $comments = str_replace($replace, $vars, $_language->module[ 'comments' ]);
                        break;
                }
            } else {
                $anzcomments = getanzcomments($ds[ 'newsID' ], 'ne');
                $replace = array('$anzcomments', '$url', '$lastposter', '$lastdate');
                $vars = array(
                    $anzcomments,
                    'index.php?site=news_comments&amp;newsID=' . $ds[ 'newsID' ],
                    clearfromtags(html_entity_decode(getlastcommentposter($ds[ 'newsID' ], 'ne'))),
                    getformatdatetime(getlastcommentdate($ds[ 'newsID' ], 'ne'))
                );

                switch ($anzcomments) {
                    case 0:
                        $comments = str_replace($replace, $vars, $_language->module[ 'no_comment' ]);
                        break;
                    case 1:
                        $comments = str_replace($replace, $vars, $_language->module[ 'comment' ]);
                        break;
                    default:
                        $comments = str_replace($replace, $vars, $_language->module[ 'comments' ]);
                        break;
                }
            }
        } else {
            $comments = '';
        }

        $tags = \webspell\Tags::getTagsLinked('news', $ds[ 'newsID' ]);

        $adminaction = '';
        if (isnewsadmin($userID)) {
            $adminaction .=
                '<a href="news.php?quickactiontype=unpublish&amp;newsID=' . $ds[ 'newsID' ] .
                '" class="btn btn-danger">' . $_language->module[ 'unpublish' ] . '</a> ';
        }
        if ((isnewswriter($userID) && $ds[ 'poster' ] == $userID) || isnewsadmin($userID)) {
            $adminaction .=
                '<input type="button" onclick="window.open(\'news.php?action=edit&amp;newsID=' . $ds[ 'newsID' ] .
                '\',\'News\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\');" value="' .
                $_language->module[ 'edit' ] . '" class="btn btn-danger">
                <input type="button" onclick="MM_confirm(\'' . $_language->module[ 'really_delete' ] .
                '\', \'news.php?action=delete&amp;id=' . $ds[ 'newsID' ] . '\')" value="' .
                $_language->module[ 'delete' ] . '" class="btn btn-danger">';
        }

        $data_array = array();
        $data_array['$newsID'] = $newsID;
        $data_array['$headline'] = $headline;
        $data_array['$rubrikname'] = $rubrikname;
        $data_array['$rubricpic'] = $rubricpic;
        $data_array['$isintern'] = $isintern;
        $data_array['$content'] = $content;
        $data_array['$adminaction'] = $adminaction;
        $data_array['$poster'] = $poster;
        $data_array['$date'] = $date;
        $data_array['$comments'] = $comments;
        $news = $GLOBALS["_template"]->replaceTemplate("news", $data_array);
        echo $news;

        $i++;

        unset($related);
        unset($comments);
        unset($lang);
        unset($ds);
    }
}
