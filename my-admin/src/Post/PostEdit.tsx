import { InputGuesser } from "@api-platform/admin";
import { TextInput, ReferenceInput, AutocompleteInput, Edit, SimpleForm } from 'react-admin';
import { Stack } from '@mui/material';

const postEditTransform = (data: any) => {
    return {
        ...data,
        author: data.author.id,
        category: data.category.id
    };
};

export const PostEdit = () => (
    <Edit sx={{ maxWidth: 500 }} transform={postEditTransform} mutationMode="undoable" warnWhenUnsavedChanges>
        <SimpleForm>
        <InputGuesser source="title" />
        <TextInput source="content" multiline />
        <InputGuesser source="excerpt" />
        <InputGuesser source="status" />
        <Stack direction="row" gap={2} width="100%">
            <ReferenceInput source="author.@id" label="Author" reference="users">
                <AutocompleteInput label="Author" filterToQuery={(searchText: string) => ({ email: searchText })} />
            </ReferenceInput>
            <ReferenceInput source="category.@id" label="Category" reference="categories">
                <AutocompleteInput label="Category" filterToQuery={(searchText: string) => ({ name: searchText })} />
            </ReferenceInput>
        </Stack>
        </SimpleForm>
    </Edit>
);
    