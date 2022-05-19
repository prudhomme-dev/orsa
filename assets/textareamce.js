let toggleState = false;
tinymce.PluginManager.add('variableApp', function (editor, url) {
    editor.ui.registry.addIcon('company', '<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M12 7V3H2v18h20V7H12zm-2 12H4v-2h6v2zm0-4H4v-2h6v2zm0-4H4V9h6v2zm0-4H4V5h6v2zm10 12h-8V9h8v10zm-2-8h-4v2h4v-2zm0 4h-4v2h4v-2z"/></svg>');
    editor.ui.registry.addIcon('date', '<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16"><g fill="currentColor"><path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zm5.402 9.746c.625 0 1.184-.484 1.184-1.18c0-.832-.527-1.23-1.16-1.23c-.586 0-1.168.387-1.168 1.21c0 .817.543 1.2 1.144 1.2z"/><path d="M16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zm-6.664-1.21c-1.11 0-1.656-.767-1.703-1.407h.683c.043.37.387.82 1.051.82c.844 0 1.301-.848 1.305-2.164h-.027c-.153.414-.637.79-1.383.79c-.852 0-1.676-.61-1.676-1.77c0-1.137.871-1.809 1.797-1.809c1.172 0 1.953.734 1.953 2.668c0 1.805-.742 2.871-2 2.871zm-2.89-5.435v5.332H5.77V8.079h-.012c-.29.156-.883.52-1.258.777V8.16a12.6 12.6 0 0 1 1.313-.805h.632z"/></g></svg>')
    editor.ui.registry.addIcon('contact', '<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M15 11h7v2h-7zm1 4h6v2h-6zm-2-8h8v2h-8zM4 19h10v-1c0-2.757-2.243-5-5-5H7c-2.757 0-5 2.243-5 5v1h2zm4-7c1.995 0 3.5-1.505 3.5-3.5S9.995 5 8 5S4.5 6.505 4.5 8.5S6.005 12 8 12z"/></svg>')
    editor.ui.registry.addIcon('profile', '<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 48 48"><g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"><path d="M14.809 34.714c6.845-1 11.558-.914 18.412.035A2.077 2.077 0 0 1 35 36.818c0 .48-.165.946-.463 1.31A61.165 61.165 0 0 1 32.941 40h2.641c.166-.198.333-.4.502-.605A4.071 4.071 0 0 0 37 36.819c0-2.025-1.478-3.77-3.505-4.05c-7.016-.971-11.92-1.064-18.975-.033c-2.048.299-3.52 2.071-3.52 4.11c0 .905.295 1.8.854 2.525c.165.214.328.424.49.63h2.577a57.88 57.88 0 0 1-1.482-1.85A2.144 2.144 0 0 1 13 36.845c0-1.077.774-1.98 1.809-2.131ZM24 25a6 6 0 1 0 0-12a6 6 0 0 0 0 12Zm0 2a8 8 0 1 0 0-16a8 8 0 0 0 0 16Z"/><path d="M24 42c9.941 0 18-8.059 18-18S33.941 6 24 6S6 14.059 6 24s8.059 18 18 18Zm0 2c11.046 0 20-8.954 20-20S35.046 4 24 4S4 12.954 4 24s8.954 20 20 20Z"/></g></svg>')
    editor.ui.registry.addMenuButton('variable', {
        text: 'Champs',
        fetch: (callback) => {
            const items = [
                {
                    type: 'nestedmenuitem',
                    text: 'Profil',
                    icon: 'profile',
                    getSubmenuItems: () => [
                        {
                            type: 'menuitem',
                            text: 'Nom Complet',
                            onAction: () => editor.insertContent('%PROFILE_NAME%')
                        },
                        {
                            type: 'menuitem',
                            text: 'Adresse Postale complète',
                            onAction: () => editor.insertContent('%PROFILE_ADDRESS%')
                        },
                        {
                            type: 'menuitem',
                            text: 'N° de téléphone fixe',
                            onAction: () => editor.insertContent('%PROFILE_PHONE%')
                        },
                        {
                            type: 'menuitem',
                            text: 'N° de téléphone mobile',
                            onAction: () => editor.insertContent('%PROFILE_PHONE_MOBILE%')
                        },
                        {
                            type: 'menuitem',
                            text: 'E-Mail de Contact',
                            onAction: () => editor.insertContent('%PROFILE_EMAIL%')
                        }
                    ]
                },
                {
                    type: 'nestedmenuitem',
                    text: 'Entreprise',
                    icon: 'company',
                    getSubmenuItems: () => [
                        {
                            type: 'menuitem',
                            text: 'Raison Sociale',
                            onAction: () => editor.insertContent('%NAMECOMPANY%')
                        },
                        {
                            type: 'menuitem',
                            text: 'Adresse Postale',
                            onAction: () => editor.insertContent('%ADDRESSCOMPANY%')
                        },
                        {
                            type: 'menuitem',
                            text: 'Adresse Postale avec contact',
                            onAction: () => editor.insertContent('%ADDRESSCOMPANYWITHCONTACT%')
                        },
                        {
                            type: 'nestedmenuitem',
                            text: 'Contact',
                            icon: 'contact',
                            getSubmenuItems: () => [
                                {
                                    type: 'menuitem',
                                    text: 'Nom complet du contact',
                                    onAction: () => editor.insertContent('%NAMECONTACT%')
                                },
                                {
                                    type: 'menuitem',
                                    text: 'Civilité du contact',
                                    onAction: () => editor.insertContent('%CIVILITYCONTACT%')
                                },
                                {
                                    type: 'menuitem',
                                    text: 'Nom du contact',
                                    onAction: () => editor.insertContent('%LASTNAMECONTACT%')
                                },
                                {
                                    type: 'menuitem',
                                    text: 'Prénom du contact',
                                    onAction: () => editor.insertContent('%FIRSTNAMECONTACT%')
                                },
                                {
                                    type: 'menuitem',
                                    text: 'N° de Téléphone fixe du contact',
                                    onAction: () => editor.insertContent('%PHONECONTACT%')
                                },
                                {
                                    type: 'menuitem',
                                    text: 'N° de Téléphone mobile du contact',
                                    onAction: () => editor.insertContent('%PHONEMOBILECONTACT%')
                                },
                                {
                                    type: 'menuitem',
                                    text: 'Adresse E-Mail du contact',
                                    onAction: () => editor.insertContent('%EMAILCONTACT%')
                                },

                            ]
                        }
                    ]
                },
                {
                    type: 'menuitem',
                    text: 'Date',
                    icon: 'date',
                    onAction: () => editor.insertContent('%DATE%')
                }
            ];
            callback(items);
        }
    });
    return {

        getMetadata: function () {
            return {
                name: 'Variables MonStage.App',
                url: 'https://cedric-prudhomme.fr'
            };
        }
    }
});

tinymce.init({
    selector: '.textareamce',
    plugins: 'variableApp preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen link codesample table charmap pagebreak nonbreaking anchor advlist lists wordcount help charmap quickbars',
    editimage_cors_hosts: ['picsum.photos'],
    menubar: 'file edit view insert format tools table help',
    toolbar: 'variable language|undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap | fullscreen  preview save print | insertfile template link anchor codesample | ltr rtl',
    toolbar_sticky: true,
    height: 500,
    content_langs: [
        {title: 'French', code: 'fr'},
        {title: 'English', code: 'en'},
        {title: 'Spanish', code: 'es'}

    ],
    language: 'fr_FR',
    automatic_uploads: true,
});