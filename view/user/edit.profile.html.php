<?php
$interests = Goteo\Model\User\Interest::getAll();
?>
                   <h3>Usuario/Perfil</h3>
                   <ol>
            			<li class="element textbox required" id="user_name">
            				<label class="title" for="UserName">Nombre completo</label>
            				<div class="contents">
            					<input type="text" name="user_name" id="UserName" value="<?php echo $user->name ?>" size="20" />
            				</div>
<?php if(isset($errors['name'])) { ?>
                            <div class="feedback" id="superform-feedback-for-user_name">
                                <div class="hint">
                                    <blockquote><?php echo $errors['name']?></blockquote>
                                </div>
                            </div>
<?php } ?>
            			</li>
            			<li class="element" id="user_avatar">
            				<label class="title" for="UserAvatar">Tu imagen</label>
<?php if(is_object($user->avatar)) { ?>
                            <img src="<?php echo $user->avatar->getLink(200, 200) ?>" alt="<?php $user->name ?>" />
<?php } ?>
            				<div class="contents">
            					<input type="file" name="user_avatar" id="UserAvatar" />
            				</div>
<?php if(isset($errors['avatar'])) { ?>
                            <div class="feedback" id="superform-feedback-for-user_avatar">
                                <div class="hint">
                                    <blockquote><?php echo $errors['avatar']?></blockquote>
                                </div>
                            </div>
<?php } ?>
            			</li>
            			<li class="element textarea" id="user_about">
            				<label class="title" for="UserAbout">Cuéntanos algo sobre ti</label>
                            <div class="contents">
                                <textarea name="user_about" id="UserAbout" cols="40" rows="4"><?php echo $user->about ?></textarea>
                            </div>
<?php if(isset($errors['about'])) { ?>
                            <div class="feedback" id="superform-feedback-for-user_about">
                                <div class="hint">
                                    <blockquote><?php echo $errors['about']?></blockquote>
                                </div>
                            </div>
<?php } ?>
                        </li>
            			<li class="element checkboxes" id="user_interests">
            				<h4 class="title">Tus intereses</h4>
                            <div class="contents">
                                <ul>
<?php foreach ($interests as $id => $value) : ?>
                                    <li><label><input type="checkbox" name="user_interests[]" value="<?php echo $id; ?>"<?php if (in_array($id, $user->interests)) echo ' checked="checked"'; ?>/> <?php echo $value; ?></label></li>
<?php endforeach; ?>
                                </ul>
                            </div>
<?php if(isset($errors['interests'])) { ?>
                            <div class="feedback" id="superform-feedback-for-user_interests">
                                <div class="hint">
                                    <blockquote><?php echo $errors['interests']?></blockquote>
                                </div>
                            </div>
<?php } ?>
            			</li>
            			<li class="element textbox" id="user_keywords">
            				<label class="title" for="UserKeywords">Palabras clave</label>
            				<div class="contents">
            					<input type="text" name="user_keywords" id="UserKeywords" value="<?php echo $user->keywords ?>" size="20" />
            				</div>
<?php if(isset($errors['keywords'])) { ?>
                            <div class="feedback" id="superform-feedback-for-user_keywords">
                                <div class="hint">
                                    <blockquote><?php echo $errors['keywords']?></blockquote>
                                </div>
                            </div>
<?php } ?>
            			</li>
            			<li class="element textarea" id="user_contribution">
            				<label class="title" for="UserContribution">Qué podrías aportar a Goteo</label>
            				<div class="contents">
            					<textarea name="user_contribution" id="UserContribution" cols="40" rows="4"><?php echo $user->contribution ?></textarea>
            				</div>
<?php if(isset($errors['contribution'])) { ?>
                            <div class="feedback" id="superform-feedback-for-user_contribution">
                                <div class="hint">
                                    <blockquote><?php echo $errors['contribution']?></blockquote>
                                </div>
                            </div>
<?php } ?>
            			</li>
            			<li class="element" id="user_webs">
            				<h4 class="title">Mis webs</h4>
            				<div class="children">
            					<div class="elements">
                					<ol>
<?php foreach ($user->webs as $web) : ?>
                                        <li>
                                            <label for="UserWebs_<?php echo $web->id; ?>"><input type="text" name="user_webs[edit][<?php echo $web->id; ?>]" value="<?php echo $web->url; ?>" /> <input type="submit" name="user_webs[remove][<?php echo $web->id; ?>]" value="Quitar" class="red" /></label>
                                        </li>
<?php endforeach; ?>
                						<li class="element submit add" id="nweb">
                							<div class="contents">
                                                <p>
                                                    <label for="UserWebs_Add">http://</label>
                                                    <input type="text" name="user_webs[add][]" id="UserWebs_Add" value="" />
                                                </p>
                								<input type="submit" name="add-user_webs" value="Nueva web" class="add" />
                							</div>
                						</li>
                					</ol>
                				</div>
                			</div>
            			</li>
            			<li class="element group" id="user_social">
            				<h4 class="title">Perfiles sociales</h4>
            				<div class="children">
            					<div class="elements">
            						<ol>
            							<li class="element textbox facebook" id="user_facebook">
            								<label class="title" for="UserFacebook">Facebook</label>
            								<div class="contents">
            									<input type="text" name="user_facebook" id="UserFacebook" class="facebook" value="<?php echo $user->facebook ?>" size="40" />
            								</div>
<?php if(isset($errors['facebook'])) { ?>
                                            <div class="feedback" id="superform-feedback-for-user_facebook">
                                                <div class="hint">
                                                    <blockquote><?php echo $errors['facebook']?></blockquote>
                                                </div>
                                            </div>
<?php } ?>
            							</li>
            							<li class="element textbox twitter" id="user_twitter">
            								<label class="title" for="UserTwitter">Twitter</label>
            								<div class="contents">
            									<input type="text" name="user_twitter" id="UserTwitter" class="twitter" value="<?php echo $user->twitter ?>" size="40" />
            								</div>
<?php if(isset($errors['twitter'])) { ?>
                                            <div class="feedback" id="superform-feedback-for-user_twitter">
                                                <div class="hint">
                                                    <blockquote><?php echo $errors['twitter']?></blockquote>
                                                </div>
                                            </div>
<?php } ?>
            							</li>
            							<li class="element textbox linkedin" id="user_linkedin">
            								<label class="title" for="UserLinkedin">LinkedIn</label>
                                            <div class="contents">
                                            	<input type="text" name="user_linkedin" id="UserLinkedin" class="linkedin" value="<?php echo $user->linkedin ?>" size="40" />
                                            </div>
<?php if(isset($errors['linkedin'])) { ?>
                                            <div class="feedback" id="superform-feedback-for-user_linkedin">
                                                <div class="hint">
                                                    <blockquote><?php echo $errors['linkedin']?></blockquote>
                                                </div>
                                            </div>
<?php } ?>
                                        </li>
            						</ol>
            					</div>
            				</div>
            			</li>
            		</ol>
