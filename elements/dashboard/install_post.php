<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;

$app = Application::getFacadeApplication();
/** @var Form $form */
/** @noinspection PhpUnhandledExceptionInspection */
$form = $app->make(Form::class);

$defaultMailAddress = null;

$u = new User();

if ($u->isRegistered()) {
    $ui = $u->getUserInfoObject();

    if ($ui instanceof UserInfo) {
        $defaultMailAddress = $ui->getUserEmail();
    }
}
?>

<!--suppress JSUnresolvedVariable, JSValidateTypes -->
<script>
    (() => {
        "use strict";
        const Utils = {
            parsePx: (value) => parseFloat(value.replace(/px/, "")),

            getRandomInRange: (min, max, precision = 0) => {
                const multiplier = Math.pow(10, precision);
                const randomValue = Math.random() * (max - min) + min;
                return Math.floor(randomValue * multiplier) / multiplier;
            },

            getRandomItem: (array) => array[Math.floor(Math.random() * array.length)],

            getScaleFactor: () => Math.log(window.innerWidth) / Math.log(1920),

            debounce: (func, delay) => {
                let timeout;
                return (...args) => {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func(...args), delay);
                };
            },
        };

        const DEG_TO_RAD = Math.PI / 180;

        const defaultConfettiConfig = {
            confettiesNumber: 250,
            confettiRadius: 6,
            confettiColors: [
                "#fcf403", "#62fc03", "#f4fc03", "#03e7fc", "#03fca5", "#a503fc", "#fc03ad", "#fc03c2"
            ],
            emojies: [],
            svgIcon: null,
        };

        class Confetti {
            constructor({initialPosition, direction, radius, colors, emojis, svgIcon}) {
                const speedFactor = Utils.getRandomInRange(0.9, 1.7, 3) * Utils.getScaleFactor();
                this.speed = {x: speedFactor, y: speedFactor};
                this.finalSpeedX = Utils.getRandomInRange(0.2, 0.6, 3);
                this.rotationSpeed = emojis.length || svgIcon ? 0.01 : Utils.getRandomInRange(0.03, 0.07, 3) * Utils.getScaleFactor();
                this.dragCoefficient = Utils.getRandomInRange(0.0005, 0.0009, 6);
                this.radius = {x: radius, y: radius};
                this.initialRadius = radius;
                this.rotationAngle = direction === "left" ? Utils.getRandomInRange(0, 0.2, 3) : Utils.getRandomInRange(-0.2, 0, 3);
                this.emojiRotationAngle = Utils.getRandomInRange(0, 2 * Math.PI);
                this.radiusYDirection = "down";

                const angle = direction === "left" ? Utils.getRandomInRange(82, 15) * DEG_TO_RAD : Utils.getRandomInRange(-15, -82) * DEG_TO_RAD;
                this.absCos = Math.abs(Math.cos(angle));
                this.absSin = Math.abs(Math.sin(angle));

                const offset = Utils.getRandomInRange(-150, 0);
                const position = {
                    x: initialPosition.x + (direction === "left" ? -offset : offset) * this.absCos,
                    y: initialPosition.y - offset * this.absSin
                };

                this.position = {...position};
                this.initialPosition = {...position};
                this.color = emojis.length || svgIcon ? null : Utils.getRandomItem(colors);
                this.emoji = emojis.length ? Utils.getRandomItem(emojis) : null;
                this.svgIcon = null;

                if (svgIcon) {
                    this.svgImage = new Image();
                    this.svgImage.src = svgIcon;
                    this.svgImage.onload = () => {
                        this.svgIcon = this.svgImage;
                    };
                }

                this.createdAt = Date.now();
                this.direction = direction;
            }

            draw(context) {
                const {x, y} = this.position;
                const {x: radiusX, y: radiusY} = this.radius;
                const scale = window.devicePixelRatio;

                if (this.svgIcon) {
                    context.save();
                    context.translate(scale * x, scale * y);
                    context.rotate(this.emojiRotationAngle);
                    context.drawImage(this.svgIcon, -radiusX, -radiusY, radiusX * 2, radiusY * 2);
                    context.restore();
                } else if (this.color) {
                    context.fillStyle = this.color;
                    context.beginPath();
                    context.ellipse(x * scale, y * scale, radiusX * scale, radiusY * scale, this.rotationAngle, 0, 2 * Math.PI);
                    context.fill();
                } else if (this.emoji) {
                    context.font = `${radiusX * scale}px serif`;
                    context.save();
                    context.translate(scale * x, scale * y);
                    context.rotate(this.emojiRotationAngle);
                    context.textAlign = "center";
                    context.fillText(this.emoji, 0, radiusY / 2);
                    context.restore();
                }
            }

            updatePosition(deltaTime, currentTime) {
                const elapsed = currentTime - this.createdAt;

                if (this.speed.x > this.finalSpeedX) {
                    this.speed.x -= this.dragCoefficient * deltaTime;
                }

                this.position.x += this.speed.x * (this.direction === "left" ? -this.absCos : this.absCos) * deltaTime;
                this.position.y = this.initialPosition.y - this.speed.y * this.absSin * elapsed + 0.00125 * Math.pow(elapsed, 2) / 2;

                if (!this.emoji && !this.svgIcon) {
                    this.rotationSpeed -= 1e-5 * deltaTime;
                    this.rotationSpeed = Math.max(this.rotationSpeed, 0);

                    if (this.radiusYDirection === "down") {
                        this.radius.y -= deltaTime * this.rotationSpeed;
                        if (this.radius.y <= 0) {
                            this.radius.y = 0;
                            this.radiusYDirection = "up";
                        }
                    } else {
                        this.radius.y += deltaTime * this.rotationSpeed;
                        if (this.radius.y >= this.initialRadius) {
                            this.radius.y = this.initialRadius;
                            this.radiusYDirection = "down";
                        }
                    }
                }
            }

            isVisible(canvasHeight) {
                return this.position.y < canvasHeight + 100;
            }
        }

        class ConfettiManager {
            constructor() {
                this.canvas = document.createElement("canvas");
                this.canvas.style = "position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1000; pointer-events: none;";
                document.body.appendChild(this.canvas);
                this.context = this.canvas.getContext("2d");
                this.confetti = [];
                this.lastUpdated = Date.now();
                window.addEventListener("resize", Utils.debounce(() => this.resizeCanvas(), 200));
                this.resizeCanvas();
                requestAnimationFrame(() => this.loop());
            }

            resizeCanvas() {
                this.canvas.width = window.innerWidth * window.devicePixelRatio;
                this.canvas.height = window.innerHeight * window.devicePixelRatio;
            }

            addConfetti(config = {}) {
                const {confettiesNumber, confettiRadius, confettiColors, emojies, svgIcon} = {
                    ...defaultConfettiConfig,
                    ...config,
                };

                const baseY = (5 * window.innerHeight) / 7;
                for (let i = 0; i < confettiesNumber / 2; i++) {
                    this.confetti.push(new Confetti({
                        initialPosition: {x: 0, y: baseY},
                        direction: "right",
                        radius: confettiRadius,
                        colors: confettiColors,
                        emojis: emojies,
                        svgIcon,
                    }));
                    this.confetti.push(new Confetti({
                        initialPosition: {x: window.innerWidth, y: baseY},
                        direction: "left",
                        radius: confettiRadius,
                        colors: confettiColors,
                        emojis: emojies,
                        svgIcon,
                    }));
                }
            }

            resetAndStart(config = {}) {
                this.confetti = [];
                this.addConfetti(config);
            }

            loop() {
                const currentTime = Date.now();
                const deltaTime = currentTime - this.lastUpdated;
                this.lastUpdated = currentTime;

                this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);

                this.confetti = this.confetti.filter((item) => {
                    item.updatePosition(deltaTime, currentTime);
                    item.draw(this.context);
                    return item.isVisible(this.canvas.height);
                });

                requestAnimationFrame(() => this.loop());
            }
        }

        const manager = new ConfettiManager();

        manager.addConfetti();

        async function subscribeToNewsletter(email) {
            const baseUrl = 'https://bitter.de/index.php/api/v1/subscribers/subscribe';

            const params = new URLSearchParams({
                mailingListId: '4',
                email: email
            });

            const url = `${baseUrl}?${params.toString()}`;

            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    ConcreteAlert.error({
                        message: <?php echo json_encode(t('Invalid status code from server.')); ?>
                    });
                }

                let data;

                try {
                    data = await response.json();
                } catch {
                    ConcreteAlert.error({
                        message: <?php echo json_encode(t('Invalid JSON response from server.')); ?>
                    });
                }

                if (Array.isArray(data.errors) && data.errors.length > 0) {
                    for (let error of data.errors) {
                        ConcreteAlert.error({
                            message: error
                        });
                    }
                } else if (data.title && data.message) {
                    ConcreteAlert.info(data);

                    jQuery.fn.dialog.closeAll();
                } else {
                    ConcreteAlert.error({
                        message: <?php echo json_encode(t('Unexpected response format.')); ?>
                    });
                }

            } catch (err) {
                ConcreteAlert.error({
                    message: err.message
                });
            }
        }

        function waitForDialogAndModify() {
            const interval = setInterval(() => {
                const dialog = document.querySelector('.ui-dialog');

                if (dialog) {
                    clearInterval(interval);

                    const titleEl = dialog.querySelector('.ui-dialog-title');

                    if (titleEl) {
                        titleEl.innerHTML = <?php echo json_encode(t("Thank you for purchasing this add-on!")); ?>;
                    }

                    const buttons = dialog.querySelectorAll('.dialog-buttons .btn');

                    buttons.forEach(button => {
                        button.innerHTML = <?php echo json_encode(t("Subscribe")); ?>;
                        button.style.color = "#ffffff";
                        button.removeAttribute('onclick');

                        button.addEventListener('click', function (event) {
                            event.preventDefault();
                            event.stopPropagation();

                            const emailInput = document.getElementById('email');
                            const email = emailInput.value.trim();

                            subscribeToNewsletter(email);

                            return false;
                        });
                    });
                }
            }, 100);
        }

        waitForDialogAndModify();
    })();
</script>

<p>
    <?php echo t("Subscribe to the newsletter and be the first to discover new features and special deals!"); ?>
</p>

<div class="form-group">
    <?php echo $form->label("email", t("Your Email Address")); ?>
    <?php echo $form->email("email", $defaultMailAddress, ["placeholder" => t("you@example.com")]); ?>
</div>

<p class="text-muted small">
    <?php /** @noinspection HtmlUnknownTarget */
    echo t(
        "By submitting this form, you agree to sign up for our newsletter and consent to the processing of your data in accordance with our %s.",
        sprintf(
            "<a href=\"%s\" target=\"_blank\">%s</a>",
            "https://bitter.de/index.php/legal/privacy-policy",
            t("privacy policy")
        )
    ); ?>
</p>