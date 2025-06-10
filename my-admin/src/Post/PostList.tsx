import { FieldGuesser, ListGuesser } from "@api-platform/admin";
import { ReferenceField, TextField } from 'react-admin';

export const PostList = () => (
    <ListGuesser>
        <FieldGuesser source="title" />
        <FieldGuesser source="excerpt" />
        <FieldGuesser source="status" />
        <ReferenceField source="author.@id" reference="users" link="show" label="Author">
            <TextField source="email" />
        </ReferenceField>
        <ReferenceField source="category.@id" reference="categories" link="show" label="Category">
            <TextField source="name" />
        </ReferenceField>
        <FieldGuesser source="viewCount" />
    </ListGuesser>
);