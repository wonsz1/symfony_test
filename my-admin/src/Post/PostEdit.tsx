import { InputGuesser, EditGuesser } from "@api-platform/admin";
import { ReferenceInput, AutocompleteInput } from 'react-admin';

const postEditTransform = (data: any) => {
    return {
        ...data,
        author: data.author.id,
        category: data.category.id
    };
};

export const PostEdit = () => (
    <EditGuesser transform={postEditTransform}>
        <InputGuesser source="title" />
        <InputGuesser source="content" />
        <InputGuesser source="excerpt" />
        <InputGuesser source="status" />
        <ReferenceInput source="author.@id" label="Author" reference="users">
            <AutocompleteInput label="Author" filterToQuery={(searchText: string) => ({ email: searchText })} />
        </ReferenceInput>
        <ReferenceInput source="category.@id" label="Category" reference="categories">
            <AutocompleteInput label="Category" filterToQuery={(searchText: string) => ({ name: searchText })} />
        </ReferenceInput>
    </EditGuesser>
);
    