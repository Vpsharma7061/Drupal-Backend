## INTRODUCTION

The Content Access by Path module allows site builders to set edit/delete
access to nodes based on paths.

For example, you want some editors to be allowed to edit all content in the
`/news` section, others to be able to edit content in `/news/business`,
others in `/news/sports`, and others in `/news/sports/rugby`.

To do so you add a taxonomy term for each of the site sections (News, News
(Business), News (Sports), News (Sports: Rugby)). Then in each term, you put
the path that user who have that taxonomy term applied can edit. So, in the
News term, you put the path `news`, in the Sports news term, you put the path
`news/sports`, and in the Rugby news term, you put the path `news/sports/rugby`.

Once that's complete, you can then edit your users and apply these taxonomy
terms to whatever users you want.

## FAQs

1. **What if I want to have more than one path per term?**
   That's fine. The path field in the taxonomy is a multivalue field, so you
   can have as many paths as you want. For example, you might have news at
   `/news` and also at `/about-us/news`. Putting these two fields into the
   News term will mean that any with that term on their user profile will
   be able to edit all items inside those paths.

2. **Can I control who can add these terms to users?**
   Sure. There's a settings page at
   `/admin/config/content/content-access-by-path` that you can use to set what
   roles have access to the field on the user profiles.

3. **Can I add more than one term to a user?**
   Yes. You can add as many terms as you want.

4. **What happens if no terms are added to a user?**
   Then that use can edit/delete whatever nodes they would ordinarily have
   access to edit/delete.

5. **I'd like to only show content in the `/admin/content` view that the**
   **current user has access to edit/delete. Can I do this?**
   Yes, just install the `content_access_by_path_admin_content` submodule,
   which will then filter the `/admin/content` view to show only content that
   the user can edit/delete. It will also show content that the user created.
   We always allow the user to edit their own content. If not, and the set the
   wrong URL on the node they will be locked out of that node until someone
   else who can edit it fixes the URL for them.

## INSTALLATION

Install as you would normally install a contributed Drupal module.
See: https://www.drupal.org/node/895232 for further information≤.

## CONFIGURATION

- There is a permission for which roles can administer the module.
- Once you decide what roles can administer the module, there's a settings page
at `admin/config/content/content-access-by-path` to say what roles can apply
taxonomy terms to users.

## MAINTAINERS

Current maintainers for Drupal 10:

- Mark Conroy (markconroy) - https://www.drupal.org/u/markconroy
