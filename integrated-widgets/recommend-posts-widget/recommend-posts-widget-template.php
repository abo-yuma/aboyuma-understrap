<?php

/**
 * Recommend Posts Widget Template
 * $data['title']
 * $data['posts']
 */

if (!empty($data['title'])) {
    echo '<h5 class="widget-title">' . $data['title'] . '</h5>';
}

if (empty($data['posts'])) {
    return;
}

foreach ($data['posts'] as $post) :
?>

    <div class="card-body" style="padding: 5px;">
        <div class="row">
            <div class="post-thumbnail col-md-5">
                <?php echo $post['thumbnail']; ?>
            </div>

            <div class="col-md-7">
                <span><?php echo $post['title']; ?></span>

                <div class="post-category" style="font-size: 0.75rem;">
                    <?php if (!empty($post['categories'])) : ?>
                        หมวดหมู่:
                        <?php
                        $cats = [];
                        foreach ($post['categories'] as $cat) {
                            $cats[] = '<a href="' . $cat['url'] . '">' . $cat['name'] . '</a>';
                        }
                        echo implode(', ', $cats);
                        ?>
                    <?php endif; ?>
                </div>

                <div class="post-thumbnail-readmore">
                    <a style="float:right;" href="<?php echo $post['permalink']; ?>">
                        อ่านบทความ...
                    </a>
                </div>
            </div>
        </div>
    </div>

<?php endforeach; ?>