import { HydraAdmin, ResourceGuesser } from '@api-platform/admin';
import { PostList } from './Post/PostList';
import { PostEdit } from './Post/PostEdit';
import { PostShow } from './Post/PostShow';
import { PostCreate } from './Post/PostCreate';
import { CategoryList } from './Category/CategoryList';
import { CategoryEdit } from './Category/CategoryEdit';

export const App = () => (
  <HydraAdmin entrypoint="http://localhost:8080/api">
    <ResourceGuesser name="posts"
    list={PostList} edit={PostEdit} show={PostShow} create={PostCreate} />
    <ResourceGuesser name="categories" list={CategoryList} edit={CategoryEdit}/>
    <ResourceGuesser name="comments" />
    <ResourceGuesser name="users" />
  </HydraAdmin>
);
