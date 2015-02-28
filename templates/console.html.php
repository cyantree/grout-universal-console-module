<?php
use Grout\AppModule\Types\AppTemplateContext;
use Grout\Cyantree\UniversalConsoleModule\Pages\Console;

/** @var $this AppTemplateContext */

/** @var Console $page */
$page = $this->task->page;

$f = $this->factory();
$q = $f->quick();
?>
<!doctype html>
<html>
<head>
    <title>WebConsole</title>
    <base href="<?= $q->e($this->app->url) ?>" />
    <meta charset="UTF-8" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <style>
        input[name=command], div.output {
            font-family: Consolas, "Courier New", Courier, monospace;
        }
        div.title {
            text-decoration: underline;
            font-weight: bold;
            margin-top: 10px;
        }
        div.success {
            color: #00aa00;
        }

        div.info {
            color: black;
        }

        div.warning {
            color: #ff8800;
        }

        div.error {
            color: #ff0000;
        }

        div.headline {
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
            margin: 15px 0 5px 0;
        }
    </style>
    <script>
        $(document).ready(function() {
            var $form = $('form');
            var $command = $('input[name=command]');
            var $output = $('div.output');
            var $status = $('div.statusLoading');
            var commandUrl = '<?= $q->e($this->task->module->getRouteUrl('console'), 'js') ?>';
            var executionKey = '<?= $page->executionKey ?>';

            function stringifyDate(date)
            {
                if (!date) {
                    date = new Date();
                }

                return date.getFullYear() + "-" + (date.getMonth() < 9 ? "0" : "") + (date.getMonth() + 1) + "-" + (date.getDate() <= 9 ? "0" : "") + date.getDate() +
                      " " + (date.getHours() <= 9 ? "0" : "") + date.getHours() + ":" + (date.getMinutes() <= 9 ? "0" : "") + date.getMinutes() + ":" +
                      (date.getSeconds() <= 9 ? "0" : "") + date.getSeconds();
            }

            function processCommandResponse(command, response)
            {
                var $d = $('<div />');
                var $headline = $('<div class="title" />');
                var $commandLink = $('<a />');
                $commandLink.prop('href', 'javascript:submitCommand("' + command.replace(/\\/g, '\\\\').replace(/"/g, '\\"') + '")');
                $commandLink.text(command);
                $headline.html(stringifyDate() + ': ');
                $headline.append($commandLink);

                $d.append($headline);

                $.each(response.messages, function() {
                    var $message = $('<div />');
                    $message.addClass(this.type);

                    if (this.message == "") {
                        this.message = " ";
                    }

                    if (this.type == "command") {
                        var $link = $('<a>');
                        $link.prop('href', 'javascript:submitCommand("' + this.command.replace(/\\/g, '\\\\').replace(/"/g, '\\"') + '")');
                        $link.text(this.title);
                        $message.html($link);

                    } else if (this.type == "url") {
                        var $link = $('<a target="_blank">');
                        $link.prop('href', this.url);
                        $link.text(this.title ? this.title : this.url);
                        $message.html($link);

                    } else {
                        $message.text(this.message);
                    }



                    $d.append($message);
                });

                $output.prepend($d);

                if (response.redirect.command) {
                    submitCommand(response.redirect.command);
                }
            }

            window.submitCommand = function submitCommand(command)
            {
                if (command == '') {
                    return;
                }

                $command.val(command);

                $status.show();

                $.ajax({
                    url: '<?= $q->e($this->task->module->getRouteUrl('console-parser'), 'js') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        command: command,
                        key: executionKey
                    },
                    success: function(response) {
                        $status.hide();

                        processCommandResponse(command, response);
                    },
                    error: function(xhr, r) {
                        $status.hide();

                        var response = {
                            messages: [{
                                type: "error",
                                message: "An unknown error occurred while processing the request."
                            }]
                        };

                        processCommandResponse(command, response);
                    }});
            };

            $form.submit(function(e) {
                submitCommand($command.val());

                e.preventDefault();
            });

            $status.hide();

            if ($command.val()) {
                submitCommand($command.val());
            }
        });
    </script>
</head>
<body>
<form action="<?=$q->e($this->task->url)?>" method="post">
    <input type="text" name="command" maxlength="255" size="150" value="<?= $q->e($page->command) ?>" />
    <a href="<?= $q->e($this->task->route->getUrl()) ?>">Home</a><br />

    <input type="submit" name="execute" value="Execute" /><br />
    <div class="warning statusLoading">Please wait...</div>
    <div class="output">

    </div>
</form>
</body>
</html>
