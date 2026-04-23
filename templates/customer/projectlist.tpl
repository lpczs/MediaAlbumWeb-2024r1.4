<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$languagecode}" xml:lang="{$languagecode}" dir="ltr">

<head>
    <title>{#str_LabelProjectList#}</title>

    {if $googletagmanagercccode ne ''}
        {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
    {/if}

    <link rel="stylesheet" type="text/css" href="{$brandroot}{asset file='/css/projectlist.css'}" media="screen"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <script src="{$webroot}/utils/jquery.js" {$nonce}></script>
    {if $extratemplate ne ''}
    {include file=$extratemplate nonce=$nonce}
    {/if}
    <script type="text/javascript" src="{$webroot}{asset file='/utils/handlebars.js'}" {$nonce}></script>
    <script id="delete-template" type="text/x-handlebars-template" {$nonce}>
        <p>{{{deletemessage}}}</p>
    </script>
    <script id="rename-template" type="text/x-handlebars-template" {$nonce}>
        <div class="text-input-wrap">
            <input type="text" id="renameName" value="{{projectname}}" class="submits" required>
            <label for="renameName">{#str_LabelProjectName#}</label>
        </div>
        <div class="text-input-error hidden"></div>
    </script>
    <script id="duplicate-template" type="text/x-handlebars-template" {$nonce}>
        <div class="text-input-wrap">
            <input type="text" id="duplicateName" value="{{projectname}}" class="submits" required>
            <label for="duplicateName">{#str_LabelProjectName#}</label>
        </div>
        <div class="text-input-error hidden"></div>
    </script>
	<script id="share-template" type="text/x-handlebars-template" {$nonce}>
        <div class="text-input-wrap text-input-wrap--with-button">
            <input type="text" id="shareLink" class="read-only" value="{{sharelink}}" readonly>
            <label for="shareLink">{#str_LabelProjectLink#}</label>
			<div class="text-input-message hidden">{#str_MessageLinkCopiedToClipboard#}</div>
			<button class="button action copylink" type="button">{#str_LabelCopyLink#}</button>
        </div>

		<p>{#str_MesssageSharePreview#}</p>
    </script>
    <script id="modal-template" type="text/x-handlebars-template" {$nonce}>
        <div class="{{id}} modal">
            {{#if title}}
            <h2 class="title">{{title}}</h2>
            {{/if}}
            <div class="content">
                {{{content}}}
            </div>
            <div class="button-wrap {{buttons.class}}">
                {{#if buttons.secondary}}
                <a class="button secondary">{{buttons.secondary}}</a>
                {{/if}}
                {{#if buttons.action}}
                <a class="button action">{{buttons.action}}</a>
                {{/if}}
            </div>
        </div>
    </script>
    <script id="keepproject-template" type="text/x-handlebars-template" {$nonce}>
        <p>{{messagetext}}</p>
    </script>
    <script type="text/javascript" {$nonce}>
        {literal}

		function onCopyLink()
		{
			var linkInput = document.getElementById('shareLink');
			var dialog = Modal.get('sharepreview');
			var message = dialog.element('.text-input-message');
			
			linkInput.select();

			document.execCommand('copy');

			// Remove the selection.
			var selection = window.getSelection();
			selection.removeAllRanges();

			linkInput.blur();
			
			// Show and hide the link copied text.
			message.removeClass('hidden');
			
			setTimeout(function()
			{
				message.addClass('hidden');
			}, 2000);
		}

        function ViewProjectList()
        {
            this.language = "{/literal}{$languagecode}{literal}";

            this.get = function(pAction, pParameters, pCallback)
            {
                var url = "?fsaction=" + pAction + "&l=" + this.language;

                if (pParameters != "")
                {
                    url += "&" + pParameters
                }

                $.get(url, pCallback);
            }

            this.generateDeviceDetectionString = function()
            {
                var screenWidth = parseInt(screen.width)/100;
                var screenHeight = parseInt(screen.height)/7;
                var screenAvailableWidth = parseInt(screen.availWidth)/5;
                var screenAvailableHeight = parseInt(screen.availHeight)/5;
                var pixRatio = 1; 
                
                if('deviceXDPI' in screen)
                { 
                    pixRatio = screen.deviceXDPI / screen.logicalXDPI;
                } 
                else if (window.hasOwnProperty('devicePixelRatio'))
                {
                pixRatio = window.devicePixelRatio;
                }
                
                if(isNaN(pixRatio))
                {
                    pixRatio = 1;
                }

                pixRatio = pixRatio *3;
        
                var deviceDetectionString = "v1s"+ screenWidth +"o"+ screenHeight + "o" + screenAvailableWidth +"o"+ screenAvailableHeight +"o"+ pixRatio +"d"; 

                //Is it a touch
                var touchType = 0; 
                //Is it a mobile
                var mobileType = 0;
                //What is the device
                var deviceType = 0;

                if ("ontouchstart" in window || navigator.msMaxTouchPoints)
                {
                    touchType = 1;
                } 

                if(navigator.userAgent.match(/Mobile| mobile/i))
                {
                    mobileType = 1;
                }

                //Apple
                if(navigator.userAgent.match(/Intel Mac/i))
                {
					// iPadOS reports it's an Intel Mac so test for touch support.
                    mobileType = touchType;
                    deviceType = 1;
                }

                if(navigator.userAgent.match(/iPad/i))
                {
                    mobileType = 1;
                    deviceType = 2;
                }

                if(navigator.userAgent.match(/iPhone/i))
                {
                    mobileType = 1;
                    deviceType = 3;
                }

                if(navigator.userAgent.match(/iPod/i))
                {
                    mobileType = 1;
                    deviceType = 4;
                }

                //Android
                if(navigator.userAgent.match(/Android/i) && navigator.userAgent.match(/Mobile| mobile/i))
                {
                    mobileType = 1;
                    deviceType = 5;
                }

                if(navigator.userAgent.match(/Android/i) && !navigator.userAgent.match(/Mobile| mobile/i))
                {
                    mobileType = 0;
                    deviceType = 6;
                }

                //Windows
                if(navigator.userAgent.match(/(Windows)/i))
                {
                    mobileType = 0;
                    deviceType = 7;
                }

                if(navigator.userAgent.match(/(Windows Phone OS|Windows CE|Windows Mobile|IEMobile)/i))
                {
                    mobileType = 1;
                    deviceType = 8;
                }

                //Blackberry
                if(navigator.userAgent.match(/BlackBerry/i))
                {
                    mobileType = 1;
                    deviceType = 9;
                }

                //Palm
                if(navigator.userAgent.match(/(palm)/i))
                {
                    mobileType = 1;
                    deviceType = 10;
                }

                //Linux
                if(navigator.userAgent.match(/(Linux|X11)/i))
                {
                    mobileType = 1;
                    deviceType = 11;
                }

                //WebOS
                if(navigator.userAgent.match(/webOS/i))
                {
                    mobileType = 1;
                    deviceType = 12;
                }

                //Opera Mini
                if(navigator.userAgent.match(/Opera Mini/i))
                {
                    mobileType = 1;
                    deviceType = 13;
                }

                sumCheck = screenWidth + screenHeight + screenAvailableWidth + screenAvailableHeight + pixRatio + touchType + mobileType + deviceType;
                deviceDetectionString = deviceDetectionString + touchType + "o" + mobileType+ "o" + deviceType + "o" + sumCheck;

                return deviceDetectionString;
            };

            this.showLoading = function(pTitle)
            {
                var title = "{/literal}{#str_MessageLoading#}{literal}";
                if (pTitle)
                {
                    title = pTitle;
                }

                var loadingDialog = Modal.get('loading');
                if (!loadingDialog)
                {
                    Modal.dialog(
                    {
                        "id": "loading",
                        "title": title,
                        "content": '<div class="animated-loader"></div>',
                        "auto": true
                    });
                }
                else
                {
                    loadingDialog.open({"title": title });
                }
            }

            this.closeDialog = function(pID)
            {
                var dialog = Modal.get(pID);
                if (dialog)
                {
                    dialog.close();
                }
            }

            this.closeError = function()
            {
                this.closeDialog('error');
            }

            this.closeLoading = function()
            {
                this.closeDialog('loading');
            }

            this.showError = function(pErrorMessage, pActionButton)
            {
                var errorDialog = Modal.get('error');
                if (!errorDialog)
                {
                    var text = "{/literal}{#str_ButtonOk#}{literal}";
                    var action;

                    if (pActionButton && pActionButton.text)
                    {
                        text = pActionButton.text;
                    }

                    if (pActionButton && pActionButton.action)
                    {
                        action = pActionButton.action;
                    }

                    Modal.dialog(
                    {
                        "id": "error",
                        "title": "{/literal}{#str_TitleError#}{literal}",
                        "content": "<p>" +  pErrorMessage + "</p>",
                        "actionButtonText": text,
                        "action": function(pModal)
                        {
                            if (action)
                            {
                                action();
                            }
                            else
                            {
                                pModal.close();
                            }
                        }.bind(this),
                        "auto": true
                    });
                }
                else
                {
                    errorDialog.open({"content": pErrorMessage});
                }
            }

            this.editProject = function(pProjectRef, pForceKill)
            {
                this.showLoading();

                $.post("?fsaction=OnlineAPI.editProject&l=" + this.language + "&dd=" + this.generateDeviceDetectionString(),
                {
                    "projectref": pProjectRef,
                    "forcekill": pForceKill
                },
                function(pResponse)
                {
                    app.closeLoading();

                    switch (pResponse.result)
                    {
                        case 0:
                        {
                            document.location = pResponse.designurl;
                            break;
                        }
                        case 6:
                        {
                            Modal.dialog(
                            {
                                "id": "continuemessage",
                                "content": "<p>" + pResponse.resultmessage + "</p>",
                                "actionButtonText": "{/literal}{#str_ButtonContinue#}{literal}",
                                "secondaryButtonText": "{/literal}{#str_ButtonCancel#}{literal}",
                                "action": function(pModal)
                                {
                                    app.editProject(pResponse.projectref, 1);

                                    pModal.close();
                                }.bind(this),
                                "auto": true
                            });
                            break;
                        }
                        default:
                        {
                            app.showError(pResponse.resultmessage);
                            break;
                        }
                    }
                });
            }

            this.connectEvents = function()
            {
                window.addEventListener('DOMContentLoaded', function(e)
                {
                  /* 
                  If the project thumbnail either doesn't exist or fails to load then
                  attempt to replace it with the product preview image. 
                  If that doesn't exist then hide the image tag. 
                  */
                  var productPreviewElements = document.getElementsByClassName('product-preview-image');
                  var productPreviewElementsLength = productPreviewElements.length;

                  var fallbackHandler = function()
                  {
                    if ((this.dataset.asset !== '') && (typeof this.dataset.asset !== 'undefined'))
                    {
                      // Load the product thumbnail image.
                      this.src = this.dataset.asset;
                      this.dataset.asset = '';
                    }
                    else
                    {
                      // Revert to the default blank image if there is no project or product thumbnail available.
                      this.src = '{/literal}{$brandroot}{literal}/images/no_image-2x.jpg';
                      this.removeEventListener('error', fallbackHandler);
                    }
                  };

                  for (var i = 0; i < productPreviewElementsLength; i++)
                  {
                    productPreviewElements[i].addEventListener('error', fallbackHandler);
                  }
                });

                $('.button.continue').click(function()
                {
                    var projectRef = $(this).parents('.panel.projects-list').attr('id');

                    app.editProject(projectRef, 0);
                });

                $('.project-options .duplicate').click(function()
                {
                    var projectref = $(this).parents('.panel.projects-list').attr('id');
                    var dialog = Modal.get('duplicate');
                    if (!dialog)
                    {
                        Modal.dialog(
                        {
                            "id": "duplicate",
                            "title": "{/literal}{#str_ButtonDuplicateProject#}{literal}",
                            "content":
                            {
                                "template": "duplicate-template",
                                "context":
                                {
                                    "projectname": $(this).parents('.panel.projects-list').data('name')
                                }
                            },
                            "actionButtonText": "{/literal}{#str_ButtonDuplicateProject#}{literal}",
                            "secondaryButtonText": "{/literal}{#str_ButtonCancel#}{literal}",
                            "action": function(pModal)
                            {
                                app.showLoading("{/literal}{#str_MessagePleaseWait#}{literal}");

                                var newName = pModal.element('#duplicateName').val().trim();
                                pModal.element('#duplicateName').val(newName);

                                app.get("OnlineAPI.duplicateProject", "projectref=" + pModal.data.projectref + "&projectname=" + encodeURIComponent(newName),
                                function(pResponse)
                                {
                                    app.closeLoading();
                                    if (pResponse.result == 0)
                                    {
                                        pModal.close();
                                        pModal.element('.text-input-error').addClass('hidden');
                                        $("html, body").animate({ scrollTop: 0 }, 500, "swing").promise().then(function()
                                        {
                                            $('.projects-list:first').before(pResponse.html);
                                            $('.projects-list:first').slideDown().removeClass('hidden');
                                            app.connectEvents();
                                        });
                                    }
                                    else
                                    {
                                        pModal.element('.text-input-error').removeClass('hidden').html(pResponse.resultmessage);
                                    }
                                });
                            },
                            "auto": true
                        }, {"projectref": projectref});
                    }
                    else
                    {
                        dialog.open(
                        {"content":{
                            "template": "duplicate-template",
                            "context":
                            {
                                "projectname": $(this).parents('.panel.projects-list').data('name')
                            }
                        }},{"projectref": projectref});
                    }
                });

                $('.project-options .delete').click(function()
                {
                    var projectref = $(this).parents('.panel.projects-list').attr('id');
                    var dialog = Modal.get('delete');
                    if (!dialog)
                    {
                        Modal.dialog({
                            "id": "delete",
                            "title": "{/literal}{#str_ButtonDeleteProject#}{literal}",
                            "content":
                            {
                                "template": "delete-template",
                                "context":
                                {
                                    "deletemessage": "{/literal}{#str_MessageDeleteProjectConfirmation#}{literal}".replace('^0', "<br/><strong>" + $(this).parents('.panel.projects-list').data('name') + "</strong>")
                                }
                            },
                            "actionButtonText": "{/literal}{#str_ButtonDeleteProject#}{literal}",
                            "secondaryButtonText": "{/literal}{#str_ButtonCancel#}{literal}",
                            "action": function(pModal)
                            {
                                pModal.close();
                                app.showLoading("{/literal}{#str_MessagePleaseWait#}{literal}");

                                app.get("OnlineAPI.deleteProject", "projectref=" + pModal.data.projectref,
                                function(pResponse)
                                {
                                    app.closeLoading();
                                    if (pResponse[pModal.data.projectref].result == 0)
                                    {
                                        $('#' + pModal.data.projectref).slideUp(400, function()
                                        {

                                            if ($('.panel.projects-list:not([style*="display: none"])').length == 0)
                                            {
                                                $(".empty-state").slideDown();
                                            }
                                        });

                                        if (typeof deleteCallBack === 'function')
                                        {
                                          deleteCallBack(pModal.data.projectref);
                                        }
                                    }
                                    else
                                    {
                                        app.showError(pResponse[pModal.data.projectref].resultmessage);
                                    }
                                });
                            },
                            "auto": true
                        }, {"projectref": projectref});
                    }
                    else
                    {
                        dialog.open(
                        {"content":{
                            "template": "delete-template",
                            "context":
                            {
                                "deletemessage": "{/literal}{#str_MessageDeleteProjectConfirmation#}{literal}".replace('^0', "<br/><strong>" + $(this).parents('.panel.projects-list').data('name') + "</strong>")
                            }
                        }}, {"projectref": projectref});
                    }
                });

                $('.project-options .rename').click(function()
                {
                    var projectref = $(this).parents('.panel.projects-list').attr('id');
                    var dialog = Modal.get('rename');
                    if (!dialog)
                    {
                        Modal.dialog({
                            "id": "rename",
                            "title": "{/literal}{#str_ButtonRenameProject#}{literal}",
                            "content":
                            {
                                "template": "rename-template",
                                "context":
                                {
                                    "projectname": $(this).parents('.panel.projects-list').data('name')
                                }
                            },
                            "actionButtonText": "{/literal}{#str_ButtonRenameProject#}{literal}",
                            "secondaryButtonText": "{/literal}{#str_ButtonCancel#}{literal}",
                            "action": function(pModal)
                            {
                                app.showLoading("{/literal}{#str_MessagePleaseWait#}{literal}");
                                var panel = $('#' + pModal.data.projectref + '.panel.projects-list');
                                var newName = pModal.element('#renameName').val().trim();
                                pModal.element('#renameName').val(newName);

                                panel.removeClass('rename-panel')

                                app.get("OnlineAPI.renameProject", "projectref=" + pModal.data.projectref + "&projectname=" + encodeURIComponent(newName),
                                function(pResponse)
                                {
                                    app.closeLoading();
                                    if (pResponse.result == 0)
                                    {
                                        pModal.close();
                                        panel.data('name', newName).addClass('rename-panel').find('#projectname').html(newName);
                                        pModal.element('.text-input-error').addClass('hidden');
                                    }
                                    else
                                    {
                                        pModal.element('.text-input-error').removeClass('hidden').html(pResponse.resultmessage);
                                    }
                                });

                            },
                            "auto": true
                        }, {"projectref": projectref});
                    }
                    else
                    {
                        dialog.open(
                        {"content":{
                            "template": "rename-template",
                            "context":
                            {
                                "projectname": $(this).parents('.panel.projects-list').data('name')
                            }
                        }}, {"projectref": projectref});
                    }
                });

                $('.project-options .share').click(function(pEvent)
                {
                  pEvent.preventDefault();
                  pEvent.stopPropagation();

                  var self = this;
                  var projectref = $(this).parents('.panel.projects-list').attr('id');

                  app.showLoading();

                  app.get("OnlineAPI.sharePreview", "projectref=" + projectref, function(pReponse)
                  {
                    app.closeLoading();

                    if (pReponse.result == '')
                    {
                      var dialog = Modal.get('sharepreview');
                      if (!dialog)
                      {
                        Modal.dialog({
                          "id": "sharepreview",
                          "title": "{/literal}{#str_TitleShareProject#}{literal}",
                          "content":
                          {
                            "template": "share-template",
                            "context":
                            {
                              "sharelink": pReponse.sharelink
                            }
                          },
                          "secondaryButtonText": "{/literal}{#str_ButtonDone#}{literal}",
                          "action": function(pModal)
                          {
                              onCopyLink();
                          },
                          "auto": true
                        }, {"projectref": projectref});
                      }
                      else
                      {
                        dialog.open(
                        {"content":{
                          "template": "share-template",
                          "context":
                          {
                            "sharelink": pReponse.sharelink
                          }
                        }}, {"projectref": projectref});
                      }
                    }
                    else
                    {
                      app.showError(pReponse.resultmessage);
                    }
                  });
                });

                $('.keepProjectLink').click(function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    var projectref = $(this).attr('data-projectref');
                    var dialog = Modal.get('keepproject');
                    if (!dialog)
                    {
                        Modal.dialog({
                            "id": "keepproject",
                            "title": "{/literal}{#str_TitleKeepProject#}{literal}",
                            "content":
                            {
                                "template": "keepproject-template",
                                "context":
                                {
                                    "messagetext": "{/literal}{#str_MessageKeepProjectWarningMessage#}{literal}"
                                }
                            },
                            "actionButtonText": "{/literal}{#str_TitleKeepProject#}{literal}",
                            "secondaryButtonText": "{/literal}{#str_ButtonCancel#}{literal}",
                            "action": function(pModal)
                            {
                                app.closeDialog('keepproject');
                                app.showLoading("{/literal}{#str_MessagePleaseWait#}{literal}");
                                var panel = $('#' + pModal.data.projectref + '.panel.projects-list');
                                $.post("?fsaction=AjaxAPI.callback&cmd=KEEPONLINEPROJECT&projectref=" + pModal.data.projectref, {}, function(pResponse) {
                                    app.closeLoading();
                                    var response = JSON.parse(pResponse);
                                    if (response.status) {
                                        var container = $('#' + response.projectref);
                                        $('.date-of-purge', container).remove();
                                    }
                                });
                            },
                            "auto": true
                        }, {"projectref": projectref});
                    }
                    else
                    {
                        dialog.open({
                            "content": {
                                "template": "keepproject-template",
                                "context":
                                {
                                    "messagetext": "{/literal}{#str_MessageKeepProjectWarningMessage#}{literal}"
                                }
                            }
                        }, {"projectref": projectref});
                    }
                });
            }
        }

        var Modal = (function()
        {
            var dialogs = [];
            var template;

            function createInstance(pConfig, pData, pTemplate)
            {
                return new TPXModal(pConfig, pData, pTemplate);
            }

            return {
                get: function(pID)
                {
                    if (dialogs[pID])
                    {
                        return dialogs[pID];
                    }
                    else
                    {
                        return null;
                    }
                },
                dialog: function(pConfig, pData)
                {
                    if (!template)
                    {
                        template = Handlebars.compile(document.getElementById("modal-template").innerHTML);
                    }

                    if (dialogs.indexOf(pConfig.id) < 0)
                    {
                        dialogs[pConfig.id] = createInstance(pConfig, pData, template);
                    }

                    return dialogs[pConfig.id];
                }
            }
        })();

        function TPXModal(pConfig, pData, pTemplate)
        {
            this.id = pConfig.id;
            this.shim = $(".shim");
            this.action = pConfig.action;
            this.data = pData;

            var buttons =
            {
                "class": "single-button"
            };

            if (pConfig.actionButtonText && pConfig.secondaryButtonText)
            {
                buttons['class'] = ""
            }

            if (pConfig.actionButtonText)
            {
                buttons['action'] = pConfig.actionButtonText;
            }

            if (pConfig.secondaryButtonText)
            {
                buttons['secondary'] = pConfig.secondaryButtonText;
            }

            this.element = function(pSelector)
            {
                return $("." + this.id + ".modal .content").find(pSelector);
            }

            this.close = function()
            {
                $("." + this.id + ".modal").hide().removeClass('dialog-open');

                if ($(".modal.dialog-open").length == 0)
                {
                    this.shim.hide();
                }
            }

            this.open = function(pConfig, pData)
            {
                if (pConfig)
                {
                    if (pConfig.content)
                    {
                        var content = pConfig.content;

                        if (typeof content == "object")
                        {
                            var contentTemplate = Handlebars.compile(document.getElementById(content.template).innerHTML);
                            content = contentTemplate(content.context);
                        }

                        $("." + this.id + ".modal .content").html(content);

                        // Ensure the button action is still applied to any button in the modal content.
                        $("." + this.id + ".modal .content .button.action").off('click').click($.proxy(function()
                        {
                            this.action(this);
                        }, this));
                    }

                    if (pConfig.title)
                    {
                        $("." + this.id + ".modal .title").html(pConfig.title);
                    }
                }

                if (pData)
                {
                    this.data = pData;
                }

                $("." + this.id + ".modal input.submits").off('keyup').keyup($.proxy(function(pEvent)
                {
                    if (pEvent.keyCode === 13)
                    {
                        // Cancel the default action, if needed
                        pEvent.preventDefault();

                        // Trigger the button element with a click
                        $("." + this.id + ".modal .button.action").click();
                    }
                }, this));

                this.shim.show();
                $("." + this.id + ".modal").show().addClass('dialog-open');
            }

            var content = "";

            if (pConfig.content)
            {
                if (typeof pConfig.content == "object")
                {
                    var contentTemplate = Handlebars.compile($("#" + pConfig.content.template).html());
                    content = contentTemplate(pConfig.content.context);
                }
                else
                {
                    content = pConfig.content;
                }
            }
            
            var context = 
            {
                "id": pConfig.id,
                "title": pConfig.title,
                "content": content,
                "buttons": buttons
            }

            // only add the dialog to the dialog container if it is unique.
            if ($('.modal-wrap .' + pConfig.id).length == 0)
            {
                $('.modal-wrap').append(pTemplate(context));
            }

            $("." + pConfig.id + ".modal .button.action").off('click').click($.proxy(function()
            {
                this.action(this);
            }, this));


            $("." + pConfig.id + ".modal .button.secondary").off('click').click($.proxy(function()
            {
                this.close();
            }, this));

            if (pConfig.auto)
            {
                this.open();
            }
        }

        var app = new ViewProjectList();

        $(function() 
        {
            {/literal}
            {if $systemerror eq false}
            {literal}

            app.connectEvents();

            {/literal}
            {elseif (($systemerror eq true) && ($invaliduser eq false))}
            {literal}

            app.showError("<p>{/literal}{#str_ErrorOccurred#}{literal}</p>", {"action": function()
            {
                document.location.reload();
            }, "text": "{/literal}{#str_ExtJsPagingToolbarRefresh#}{literal}"});

            {/literal}
            {/if}
            {literal}
        });
        {/literal}
    </script>

</head>

<body>

    <header>
        <img class="logo-image" src="{$headerlogoasset}"/>
        {if $returntext != ''}
            <a href="/?fsaction=OnlineAPI.leaveUsersProjectList" class="back-link">{$returntext}</a>
        {/if}
    </header>
    
    <section class="content-wrap">
    
        <h1>{#str_LabelProjectList#}</h1>

        {if $projects|@sizeof > 0}

        {foreach from=$projects item=project}
            {include file=$projectrowtemplate project=$project}
        {/foreach}
        
        {/if}
        
        <div class="empty-state" {($projects|@sizeof > 0)?"style=\"display: none\"":""}>
            <p>{#str_MessageNoProjectsInList#}</p>
        </div>

    </section>

    <div class="shim">

        <div class="modal-wrap">
        </div>

    </div>

</body>
</html>