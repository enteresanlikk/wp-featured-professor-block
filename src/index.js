import "./index.scss"
import { useSelect } from "@wordpress/data"
import { useState, useEffect } from "react"
import apiFetch from "@wordpress/api-fetch"
const __ = wp.i18n.__;

wp.blocks.registerBlockType("ourplugin/featured-professor", {
    title: "Professor Callout",
    description: "Include a short description and link to a professor of your choice",
    icon: "welcome-learn-more",
    category: "common",
    attributes: {
        professorId: {
            type: "string",
        }
    },
    edit: EditComponent,
    save: function () {
        return null
    }
});

function EditComponent(props) {
    const [preview, setPreview] = useState(null);

    useEffect(() => {
        if(props.attributes.professorId) {
            (async () => {
                const response = await apiFetch({
                    path: `/featuredProfessor/v1/getHTML?id=${props.attributes.professorId}`
                });

                setPreview(response);
                updateMetaData();
            })();
        }
    }, [props.attributes.professorId]);

    useEffect(() => {
        return () => {
            updateMetaData();
        };
    });

    function updateMetaData() {
        const professorIds = wp.data.select('core/block-editor')
            .getBlocks()
            .filter(block => block.name == 'ourplugin/featured-professor')
            .map(block => block.attributes.professorId)
            .filter((item, index, arr) => {
                return arr.indexOf(item) == index && item != '';
            });

        wp.data.dispatch('core/editor').editPost({
            meta: {
                'featured_professor': professorIds
            }
        });
    }

    const professors = useSelect(select => {
        return select('core').getEntityRecords('postType', 'professor', { per_page: -1 })
    });

    if(professors == undefined) {
        return <p>Loading...</p>
    }

    return (
        <div className="featured-professor-wrapper">
            <div className="professor-select-container">
                <select onChange={e => props.setAttributes({
                    professorId: e.target.value
                })}>
                    <option value="">{__('Select a professor', 'featured-professor')}</option>
                    {professors.map(professor => {
                        return (
                            <option
                                value={professor.id}
                                selected={professor.id == props.attributes.professorId}>
                                {professor.title.rendered}
                            </option>
                        )
                    })}
                </select>
            </div>
            {props.attributes.professorId && (<div dangerouslySetInnerHTML={{ __html: preview }}></div>)}
        </div>
    )
}