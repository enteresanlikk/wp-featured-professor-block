<?php

function generateProfessorHTML($professorId) {
    $professor = new WP_Query([
        'post_type' => 'professor',
        'p' => $professorId
    ]);

    while($professor->have_posts()) {
        $professor->the_post();
        ob_start(); ?>
            <div class="professor-callout">
                <div class="professor-callout__photo" style="background-image: url(<?php the_post_thumbnail_url('professor_portrait'); ?>)"></div>
                <div class="professor-callout__text">
                    <h5>
                        <?php the_title(); ?>
                    </h5>
                    <p>
                        <?php echo wp_trim_words(get_the_content(), 30); ?>
                    </p>
                    <p>
                        <a href="<?php the_permalink(); ?>" class="nu gray">
                            View Professor
                        </a>
                    </p>
                </div>
            </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
}