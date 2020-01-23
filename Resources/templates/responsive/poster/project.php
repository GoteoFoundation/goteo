<style>
    @import url(https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic|Open+Sans+Condensed:300|Kalam);

    h1,h2,h3,h4,h5,h6,p {
        margin: 0;
        padding: 0 10mm 10mm 10mm;
        line-height: 1.2;
    }

    h1{
        font-size: 32px;
        color: #149290;
        text-align: center;
        margin-top: 30px;
    }

    h3{
        margin-top: 15mm;
    }

    h4 {
        font-weight: normal;
        text-align: center;
    }

    img {
        max-width: 100%;
    }

</style>

<page backtop="0mm">
    <div style="font-size: 16px">
        <div style="width:90%; margin-left:5%; background: #19b4b2; color: #FFF; padding-bottom: 40mm">
            <p style="font-size: 42px; margin-top: 15mm">
                <?= $this->text('poster-call-for-action') ?>
            </p>

            <p style="text-align: justify;width: 60%;margin-left: 9mm;">
                <?= $this->text('poster-subtitle') ?>
            </p>
        </div>
        <div style="background: #57246f; width: 25mm; height: 25mm; position: absolute; left: 35mm; top: 70mm"></div>
        <?php
            if($this->project->media->url) {
                if(!empty($this->project->secGallery['play-video'][0])) {
                    $img_url=$this->project->secGallery['play-video'][0]->imageData->getLink(780, 478);
                }
            } else {
                $img_url = $this->project->image->getLink(780, 478);
            }
        ?>

        <img class="center" src="<?= $img_url ?>" style="position:absolute; z-index: 1; left: 40mm; top:75mm; width:120mm;">

        <div style="width:75%; margin-left:12%;">
            <h3><?= $this->text('poster-support-project'); ?></h3>
            <h1> <?= $this->project->name ?> </h1>
            <h4> <?= $this->project->subtitle ?> </h4>
        </div>

        <div style="border-left: black;border-left-style: solid;border-left-width: 2px; width: 60%; margin-left: 30mm; margin-top: 40mm;">
            <p>
                <?= $this->text('poster-donate', $this->project->id) ?>
            </p>
        </div>

        <div>
            <p>

            </p>
        </div>
    </div>
</page>